<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ImageGenerator
{
    private Client $httpClient;
    private string $apiKey;
    private string $apiEndpoint = 'https://generativelanguage.googleapis.com/v1beta/models/imagen-3.0-generate-001:generate';

    public function __construct()
    {
        $this->apiKey = $_ENV['NANO_BANANA_API_KEY'] ?? '';
        $this->httpClient = new Client([
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    /**
     * Generate an image based on blog post content
     * Returns image URL or null if generation fails
     */
    public function generateImageForPost(string $title, string $excerpt): ?string
    {
        if (empty($this->apiKey)) {
            error_log('Nano Banana API key not configured');
            return null;
        }

        // Create a visual prompt from the blog post
        $prompt = $this->buildImagePrompt($title, $excerpt);
        
        try {
            $imageData = $this->callNanoBananaAPI($prompt);
            
            if ($imageData) {
                // Save image locally
                return $this->saveImage($imageData, $title);
            }
            
            return null;
        } catch (\Exception $e) {
            error_log('Image generation failed: ' . $e->getMessage());
            return null;
        }
    }

    private function buildImagePrompt(string $title, string $excerpt): string
    {
        // Create a cinematic, dark, moody prompt that fits The Fifth State aesthetic
        $cleanTitle = strip_tags($title);
        $cleanExcerpt = strip_tags($excerpt);
        
        // Extract key themes
        $themes = $this->extractThemes($cleanTitle);
        
        return <<<PROMPT
Create a cinematic, moody photograph with dark aesthetic for an article titled "{$cleanTitle}".

Style requirements:
- Dark, muted color palette (blacks, deep blues, charcoal grays)
- High contrast, dramatic lighting
- Minimalist composition
- Professional photography quality
- Shallow depth of field
- Film grain texture
- Moody atmosphere

Theme: {$themes}

The image should evoke: introspection, transformation, masculine energy, stoic philosophy, personal growth, overcoming adversity.

Visual style: Similar to a Visualize Value aesthetic meets modern noir photography. Think: architectural lines, solitary figures, urban landscapes at dusk, minimal design elements, powerful negative space.

No text, no graphics, pure photographic composition.
PROMPT;
    }

    private function extractThemes(string $title): string
    {
        $lowerTitle = strtolower($title);
        
        if (str_contains($lowerTitle, 'rejection') || str_contains($lowerTitle, 'worth')) {
            return "A lone figure standing at the edge of a dark urban rooftop at dusk";
        }
        if (str_contains($lowerTitle, 'time') || str_contains($lowerTitle, 'behind')) {
            return "Clock hands in extreme close-up with blurred cityscape";
        }
        if (str_contains($lowerTitle, 'numbness') || str_contains($lowerTitle, 'anhedonia')) {
            return "Empty modern room with single chair, dramatic window light";
        }
        if (str_contains($lowerTitle, 'codependency') || str_contains($lowerTitle, 'identity')) {
            return "Reflection in water or mirror, fragmented self-image";
        }
        if (str_contains($lowerTitle, 'addiction') || str_contains($lowerTitle, 'dopamine')) {
            return "Dark alleyway with neon signs, urban noir atmosphere";
        }
        if (str_contains($lowerTitle, 'burnout') || str_contains($lowerTitle, 'exhausted')) {
            return "Dramatic shadows of window blinds on empty wall";
        }
        if (str_contains($lowerTitle, 'career') || str_contains($lowerTitle, 'skills')) {
            return "Architectural concrete staircase ascending into light";
        }
        if (str_contains($lowerTitle, 'comparison') || str_contains($lowerTitle, 'peers')) {
            return "City skyline at night from below, looking up";
        }
        
        // Default theme
        return "Minimalist dark abstract composition with dramatic lighting";
    }

    private function callNanoBananaAPI(string $prompt): ?string
    {
        try {
            $response = $this->httpClient->post($this->apiEndpoint, [
                'query' => [
                    'key' => $this->apiKey
                ],
                'json' => [
                    'prompt' => $prompt,
                    'number_of_images' => 1,
                    'aspect_ratio' => '16:9', // Widescreen for hero images
                    'negative_prompt' => 'text, watermark, signature, low quality, blurry, amateur',
                    'person_generation' => 'allow_adult'
                ]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            
            // Extract image data from response
            if (isset($body['images'][0]['image'])) {
                return $body['images'][0]['image']; // Base64 encoded image
            }
            
            error_log('Unexpected API response format: ' . json_encode($body));
            return null;
            
        } catch (GuzzleException $e) {
            error_log('Nano Banana API request failed: ' . $e->getMessage());
            throw new \RuntimeException('Image generation failed: ' . $e->getMessage());
        }
    }

    private function saveImage(string $imageData, string $title): string
    {
        // Create images directory if it doesn't exist
        $imagesDir = __DIR__ . '/../public/images/posts';
        if (!file_exists($imagesDir)) {
            mkdir($imagesDir, 0755, true);
        }

        // Generate unique filename from title
        $filename = $this->generateFilename($title);
        $filepath = $imagesDir . '/' . $filename;

        // Decode base64 and save
        $imageContent = base64_decode($imageData);
        file_put_contents($filepath, $imageContent);

        // Return relative URL for database storage
        return '/images/posts/' . $filename;
    }

    private function generateFilename(string $title): string
    {
        // Create SEO-friendly filename from title
        $filename = strtolower($title);
        $filename = preg_replace('/[^a-z0-9]+/', '-', $filename);
        $filename = trim($filename, '-');
        $filename = substr($filename, 0, 50); // Limit length
        
        // Add timestamp for uniqueness
        $filename .= '-' . time();
        
        return $filename . '.jpg';
    }

    /**
     * Get fallback image URL if generation fails
     */
    public function getFallbackImage(): string
    {
        // Use a dark abstract gradient as fallback
        return 'https://images.unsplash.com/photo-1557683316-973673baf926?w=1920&q=80';
    }
}

