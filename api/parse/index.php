<?php

function convertJsonToCss($url) {
    $jsonContent = file_get_contents($url);
    
    if ($jsonContent === false) {
        return "Error: Unable to fetch the JSON file from the provided URL.";
    }

    $themeData = json_decode($jsonContent, true);
    
    if ($themeData === null) {
        return "Error: Unable to decode the JSON data.";
    }

    $schema = isset($themeData['$schema']) ? $themeData['$schema'] : 'Unknown Schema';
    $name = isset($themeData['name']) ? $themeData['name'] : 'Unknown Theme';
    $author = isset($themeData['author']) ? $themeData['author'] : 'Unknown Author';
    $email = isset($themeData['email']) ? $themeData['email'] : 'Unknown Email';

    $requiredKeys = [
        'name', 'author', 'email',
        'colors.header.bg', 'colors.header.button', 'colors.header.button:hover',
        'colors.root.bg', 'colors.root.section',
        'colors.product.bg', 'colors.product.btn:primary', 'colors.product.btn:secondary'
    ];

    $missingKeys = checkRequiredKeys($themeData, $requiredKeys);
    
    if (!empty($missingKeys)) {
        return "Error: Missing required keys: " . implode(", ", $missingKeys);
    }

    $generatedAt = date('H:i, d.m.Y (T)');

    $css = "/* auto-generated via quickshop Themes Store @ Copyright © 2024-2025 xKotelek https://kotelek.dev */\n";
    $css .= "/* Generated at $generatedAt */\n\n";
    $css .= "/* quickshop theme @ metadata */\n";
    $css .= ":quickshop-theme-metadata {\n";
    $css .= "    --qs-theme-name: \"$name\";\n";
    $css .= "    --qs-theme-author: \"$author\";\n";
    $css .= "    --qs-theme-email: \"$email\";\n";
    $css .= "}\n\n";
    $css .= ":root {\n";

    if (isset($themeData['colors'])) {
        $colors = $themeData['colors'];
        foreach ($colors as $category => $colorValues) {
            if (is_array($colorValues)) {
                foreach ($colorValues as $key => $value) {
                    $cssKey = str_replace([':', '.', ','], ['-', ':', ''], strtolower($key));
                    $css .= "    --qs-" . strtolower($category) . "-" . strtolower(str_replace('.', '-', $cssKey)) . ": $value;\n";
                }
            }
        }
    }

    $css .= "}\n";
    
    return $css;
}

function checkRequiredKeys($themeData, $requiredKeys) {
    $missingKeys = [];

    foreach ($requiredKeys as $key) {
        $keyParts = explode('.', $key);
        $value = $themeData;

        foreach ($keyParts as $part) {
            if (isset($value[$part])) {
                $value = $value[$part];
            } else {
                $missingKeys[] = $key;
                break;
            }
        }
    }

    return $missingKeys;
}

$url = isset($_GET['url']) ? $_GET['url'] : 'https://raw.githubusercontent.com/quick-systems/quickshop-themes/refs/heads/main/themes/$schema.json';

header("Content-Type: text/css");
echo convertJsonToCss($url);

?>
