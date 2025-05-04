<?php

function convertJsonToCss($url) {
    $jsonContent = file_get_contents($url);
    
    if ($jsonContent === false) {
        return "Error: Unable to fetch the JSON file from the provided URL.";
    }

    $themeData = json_decode($jsonContent, true);
    
    if (!isset($themeData['$schema']) || $themeData['$schema'] !== "https://raw.githubusercontent.com/quick-systems/quickshop-themes/main/themes/__template__.json") {
        return "Error: Invalid or missing schema in the JSON data.";
    }

    $name = isset($themeData['name']) ? $themeData['name'] : 'Unknown Theme';
    $author = isset($themeData['author']) ? $themeData['author'] : 'Unknown Author';
    $email = isset($themeData['email']) ? $themeData['email'] : 'Unknown Email';

    $css = "/* auto-generated via quickshop Themes Store @ Copyright © 2024-2025 xKotelek https://kotelek.dev */\n";
    $css .= "/* Generated at " . date('H:i, d.m.Y (T)') . " */\n\n";
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
            foreach ($colorValues as $key => $value) {
                $css .= "    --qs-" . strtolower($category) . "-" . strtolower(str_replace('.', '-', $key)) . ": $value; /* \"$key\": \"$value\" */\n";
            }
        }
    }

    $css .= "}\n";
    
    return $css;
}

$url = isset($_GET['url']) ? $_GET['url'] : 'https://raw.githubusercontent.com/quick-systems/quickshop-themes/refs/heads/main/themes/__template__.json';

header("Content-Type: text/css");
echo convertJsonToCss($url);

?>
