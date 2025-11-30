<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ProductRecommender
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

    public function recommendProducts(string $postTitle, string $postContent): array
    {
        $prompt = $this->buildPrompt($postTitle, $postContent);
        
        try {
            $response = $this->callClaudeAPI($prompt);
            $products = $this->parseProductResponse($response);
            
            // Add Amazon affiliate links
            foreach ($products as &$product) {
                $product['amazon_link'] = $this->generateAmazonLink(
                    $product['title'], 
                    $product['asin'] ?? null
                );
            }
            
            return [
                'success' => true,
                'products' => $products
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'products' => []
            ];
        }
    }

    private function buildPrompt(string $title, string $content): string
    {
        $cleanContent = strip_tags($content);
        $cleanContent = substr($cleanContent, 0, 1000);
        
        return <<<PROMPT
Based on this blog post for ambitious men in their 20s-30s who feel stuck:

Title: {$title}
Content: {$cleanContent}

Recommend 6-8 REAL Amazon products that would directly help readers implement the advice in this post.

PRODUCT CATEGORIES TO INCLUDE:
1. **Books** (2-3): Self-development, psychology, career books
2. **Tools/Gear**: Journals, planners, fitness equipment, productivity tools
3. **Supplements/Health**: If relevant to mental clarity, energy, sleep, or fitness
4. **Technology**: Apps, devices, or tools that support the post's goals

REQUIREMENTS:
- Each product must have a REAL Amazon ASIN
- Products must be directly actionable (help them DO the thing)
- Mix of price points (\$10-\$100 range)
- No generic products - be specific

For EACH product provide:
1. Product Type (BOOK, JOURNAL, SUPPLEMENT, FITNESS, TECH, TOOL)
2. Title (exact product name)
3. Brand/Author (manufacturer or author name)
4. ASIN (10-character Amazon product ID - MUST BE REAL)
5. Relevance (one sentence: HOW it helps with THIS specific post)

FORMAT EXACTLY LIKE THIS:

PRODUCT 1
Type: BOOK
Title: Atomic Habits
Brand: James Clear
ASIN: B01N5AX61W
Relevance: Provides the step-by-step framework for building the daily discipline habits discussed in this post.

PRODUCT 2
Type: JOURNAL
Title: The Five Minute Journal
Brand: Intelligent Change
ASIN: B073BJWB1M
Relevance: Structured daily reflection tool to track progress on the mental clarity exercises mentioned.

PRODUCT 3
Type: SUPPLEMENT
Title: Thorne Research Magnesium Bisglycinate
Brand: Thorne
ASIN: B0797H7LPT
Relevance: Supports better sleep quality which is critical for the nervous system regulation discussed.

PRODUCT 4
Type: FITNESS
Title: TRX ALL-IN-ONE Suspension Training System
Brand: TRX
ASIN: B073WZ4P45
Relevance: Minimal equipment for building the physical discipline routine outlined in the action steps.

Now provide 6-8 product recommendations for this post:
PROMPT;
    }

    private function callClaudeAPI(string $prompt): string
    {
        try {
            $response = $this->httpClient->post(CLAUDE_API_URL, [
                'json' => [
                    'model' => CLAUDE_MODEL,
                    'max_tokens' => 2048,
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

    private function parseProductResponse(string $response): array
    {
        $products = [];
        
        // Parse with product type
        preg_match_all('/PRODUCT \d+\s+Type:\s*(.+?)\s+Title:\s*(.+?)\s+Brand:\s*(.+?)\s+ASIN:\s*([A-Z0-9]{10})\s+Relevance:\s*(.+?)(?=PRODUCT \d+|$)/s', $response, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            if (count($match) >= 6) {
                $products[] = [
                    'type' => trim($match[1]),
                    'title' => trim($match[2]),
                    'author' => trim($match[3]), // Using 'author' field for brand/author
                    'asin' => trim($match[4]),
                    'relevance_note' => trim($match[5])
                ];
            }
        }
        
        return array_slice($products, 0, 8); // Max 8 products
    }

    private function generateAmazonLink(string $title, ?string $asin = null): string
    {
        $affiliateId = AMAZON_AFFILIATE_ID;
        
        if (!empty($asin)) {
            if (!empty($affiliateId)) {
                return "https://www.amazon.com/dp/{$asin}?tag={$affiliateId}";
            } else {
                return "https://www.amazon.com/dp/{$asin}";
            }
        }
        
        $searchQuery = urlencode($title);
        if (empty($affiliateId)) {
            return "https://www.amazon.com/s?k={$searchQuery}";
        }
        
        return "https://www.amazon.com/s?k={$searchQuery}&tag={$affiliateId}";
    }
}

