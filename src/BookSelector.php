<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BookSelector
{
    private Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout' => 60,
            'headers' => [
                'x-api-key' => CLAUDE_API_KEY,
                'anthropic-version' => CLAUDE_API_VERSION,
                'content-type' => 'application/json',
            ]
        ]);
    }

    public function selectBooks(string $postTitle, string $postContent): array
    {
        $prompt = $this->buildPrompt($postTitle, $postContent);
        
        try {
            $response = $this->callClaudeAPI($prompt);
            $books = $this->parseBookResponse($response);
            
            // Add Amazon affiliate links
            foreach ($books as &$book) {
                $book['amazon_link'] = $this->generateAmazonLink(
                    $book['title'], 
                    $book['author'],
                    $book['asin'] ?? null
                );
            }
            
            return [
                'success' => true,
                'books' => $books
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'books' => []
            ];
        }
    }

    private function buildPrompt(string $title, string $content): string
    {
        // Strip HTML for cleaner prompt
        $cleanContent = strip_tags($content);
        $cleanContent = substr($cleanContent, 0, 1000); // Limit context length
        
        return <<<PROMPT
Based on this blog post for ambitious men in their 20s and 30s who feel stuck or directionless:

Title: {$title}
Content excerpt: {$cleanContent}

Recommend 3 real, published books that would help readers take action on this topic. Focus on books about:
- Self-mastery and discipline (James Clear, Ryan Holiday, Naval Ravikant style)
- Career development and purpose (Cal Newport, Mark Manson)
- Mental health and mindset (Stoicism, modern psychology)
- Building a meaningful life

These should be actual books that exist and are available on Amazon.

For each book, provide:
1. The exact book title
2. The author's name
3. The Amazon ASIN (10-character product ID - CRITICAL: Must be the actual ASIN from Amazon)
4. A brief (one sentence) explanation of why it's relevant to this specific topic

Format your response EXACTLY like this example:

BOOK 1
Title: Atomic Habits
Author: James Clear
ASIN: B01N5AX61W
Relevance: Provides the practical framework for building the daily discipline discussed in this post.

BOOK 2
Title: The Obstacle Is the Way
Author: Ryan Holiday
ASIN: B00G3L1B8K
Relevance: Offers the Stoic perspective on turning setbacks into opportunities for growth.

BOOK 3
Title: So Good They Can't Ignore You
Author: Cal Newport
ASIN: B0076DDBJ6
Relevance: Challenges the "follow your passion" myth and provides a strategic approach to career development.

Now provide your 3 book recommendations:
PROMPT;
    }

    private function callClaudeAPI(string $prompt): string
    {
        try {
            $response = $this->httpClient->post(CLAUDE_API_URL, [
                'json' => [
                    'model' => CLAUDE_MODEL,
                    'max_tokens' => 1024,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ]
                ]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            
            if (isset($body['content'][0]['text'])) {
                return $body['content'][0]['text'];
            }
            
            throw new \RuntimeException('Unexpected API response format');
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Claude API request failed: ' . $e->getMessage());
        }
    }

    private function parseBookResponse(string $response): array
    {
        $books = [];
        
        // Parse the structured response with ASIN
        preg_match_all('/BOOK \d+\s+Title:\s*(.+?)\s+Author:\s*(.+?)\s+ASIN:\s*([A-Z0-9]{10})\s+Relevance:\s*(.+?)(?=BOOK \d+|$)/s', $response, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            if (count($match) >= 5) {
                $books[] = [
                    'title' => trim($match[1]),
                    'author' => trim($match[2]),
                    'asin' => trim($match[3]),
                    'relevance_note' => trim($match[4])
                ];
            }
        }
        
        // Fallback: if parsing fails, return empty array
        // The system will handle this gracefully
        return array_slice($books, 0, 3); // Ensure max 3 books
    }

    private function generateAmazonLink(string $title, string $author, ?string $asin = null): string
    {
        $affiliateId = AMAZON_AFFILIATE_ID;
        
        // If we have an ASIN, use direct product link (preferred)
        if (!empty($asin)) {
            if (!empty($affiliateId)) {
                // Direct product link with affiliate tag
                return "https://www.amazon.com/dp/{$asin}?tag={$affiliateId}";
            } else {
                // Direct product link without affiliate tag
                return "https://www.amazon.com/dp/{$asin}";
            }
        }
        
        // Fallback: Generate Amazon search URL
        $searchQuery = urlencode($title . ' ' . $author);
        
        if (empty($affiliateId)) {
            return "https://www.amazon.com/s?k={$searchQuery}";
        }
        
        return "https://www.amazon.com/s?k={$searchQuery}&tag={$affiliateId}";
    }
}

