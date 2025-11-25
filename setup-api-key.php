<?php

/**
 * Quick setup script to add Nano Banana API key to .env file
 */

$apiKey = 'AIzaSyD0tYbHJhSineNg0OLp4XSMIM7lAZQXrUE';

$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    echo "ERROR: .env file not found. Please copy env.example to .env first.\n";
    exit(1);
}

$envContent = file_get_contents($envFile);

// Check if key already exists
if (strpos($envContent, 'NANO_BANANA_API_KEY') !== false) {
    // Update existing key
    $envContent = preg_replace(
        '/NANO_BANANA_API_KEY=.*/m',
        "NANO_BANANA_API_KEY={$apiKey}",
        $envContent
    );
    echo "✓ Updated existing NANO_BANANA_API_KEY in .env\n";
} else {
    // Add new key
    $envContent .= "\n# Nano Banana (Google Imagen) API for image generation\n";
    $envContent .= "NANO_BANANA_API_KEY={$apiKey}\n";
    echo "✓ Added NANO_BANANA_API_KEY to .env\n";
}

file_put_contents($envFile, $envContent);

echo "\n";
echo "API Key configured successfully!\n";
echo "You can now generate AI images for your blog posts.\n";

