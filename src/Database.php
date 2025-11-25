<?php

namespace App;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private string $dbPath;

    public function __construct(string $dbPath = null)
    {
        $this->dbPath = $dbPath ?? __DIR__ . '/../database/blog.db';
        $this->initializeDatabase();
    }

    public static function getInstance(string $dbPath = null): PDO
    {
        if (self::$instance === null) {
            $instance = new self($dbPath);
            self::$instance = $instance->connect();
        }
        return self::$instance;
    }

    private function connect(): PDO
    {
        try {
            $pdo = new PDO('sqlite:' . $this->dbPath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        } catch (PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    private function initializeDatabase(): void
    {
        $pdo = $this->connect();

        // Create posts table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                content TEXT NOT NULL,
                excerpt TEXT NOT NULL,
                image_url TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                published BOOLEAN DEFAULT 1
            )
        ');

        // Create books table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS books (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                post_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                author TEXT NOT NULL,
                amazon_link TEXT NOT NULL,
                relevance_note TEXT,
                FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
            )
        ');

        // Create generation_log table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS generation_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                attempted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                success BOOLEAN DEFAULT 0,
                error_message TEXT
            )
        ');

        // Create index for better query performance
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_posts_created_at ON posts(created_at DESC)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_books_post_id ON books(post_id)');
    }

    public function getLastPostTime(): ?string
    {
        $pdo = self::getInstance($this->dbPath);
        $stmt = $pdo->query('SELECT created_at FROM posts ORDER BY created_at DESC LIMIT 1');
        $result = $stmt->fetch();
        return $result ? $result['created_at'] : null;
    }

    public function getAllPosts(int $limit = 50, bool $includeContent = false): array
    {
        $pdo = self::getInstance($this->dbPath);
        $fields = $includeContent ? 'id, title, content, excerpt, created_at' : 'id, title, excerpt, created_at';
        $stmt = $pdo->prepare("
            SELECT {$fields}
            FROM posts 
            WHERE published = 1 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getPostById(int $id): ?array
    {
        $pdo = self::getInstance($this->dbPath);
        $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ? AND published = 1');
        $stmt->execute([$id]);
        $post = $stmt->fetch();
        return $post ?: null;
    }

    public function getBooksByPostId(int $postId): array
    {
        $pdo = self::getInstance($this->dbPath);
        $stmt = $pdo->prepare('SELECT * FROM books WHERE post_id = ?');
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    }

    public function createPost(string $title, string $content, string $excerpt, ?string $imageUrl = null): int
    {
        $pdo = self::getInstance($this->dbPath);
        $stmt = $pdo->prepare('
            INSERT INTO posts (title, content, excerpt, image_url, created_at, published) 
            VALUES (?, ?, ?, ?, datetime("now"), 1)
        ');
        $stmt->execute([$title, $content, $excerpt, $imageUrl]);
        return (int)$pdo->lastInsertId();
    }

    public function updatePostImage(int $postId, string $imageUrl): void
    {
        $pdo = self::getInstance($this->dbPath);
        $stmt = $pdo->prepare('UPDATE posts SET image_url = ? WHERE id = ?');
        $stmt->execute([$imageUrl, $postId]);
    }

    public function createBook(int $postId, string $title, string $author, string $amazonLink, string $relevanceNote): void
    {
        $pdo = self::getInstance($this->dbPath);
        $stmt = $pdo->prepare('
            INSERT INTO books (post_id, title, author, amazon_link, relevance_note) 
            VALUES (?, ?, ?, ?, ?)
        ');
        $stmt->execute([$postId, $title, $author, $amazonLink, $relevanceNote]);
    }

    public function deleteBooksByPostId(int $postId): void
    {
        $pdo = self::getInstance($this->dbPath);
        $stmt = $pdo->prepare('DELETE FROM books WHERE post_id = ?');
        $stmt->execute([$postId]);
    }

    public function logGeneration(bool $success, ?string $errorMessage = null): void
    {
        $pdo = self::getInstance($this->dbPath);
        $stmt = $pdo->prepare('
            INSERT INTO generation_log (attempted_at, success, error_message) 
            VALUES (datetime("now"), ?, ?)
        ');
        $stmt->execute([$success ? 1 : 0, $errorMessage]);
    }
}


