<?php

// --- CONFIG ---
$templateDir = __DIR__ . '/templates/';
$contentDir  = __DIR__ . '/pages/';

// --- HELPERS ---
function loadTemplate($file) {
    if (!file_exists($file)) {
        return "<!-- Template not found: $file -->";
    }
    return file_get_contents($file);
}

function resolveTags($template, $contentDir) {
    // Matches tags like {{file:php_manuals.html}}
    return preg_replace_callback('/\{\{file:([\w\.\-\/]+)\}\}/', function($matches) use ($contentDir) {
        $filename = basename($matches[1]); // Prevent directory traversal
        $filePath = $contentDir . $filename;
        return file_exists($filePath) ? file_get_contents($filePath) : "<!-- Missing file: $filename -->";
    }, $template);
}

// --- MAIN ---
$id = isset($_GET['id']) ? $_GET['id'] : 'online_community';
$templateFile = $templateDir . 'main.html';

// Load and resolve
$templateContent = loadTemplate($templateFile);

// Replace {{file:<id>.html}} with file content
$resolved = resolveTags($templateContent, $contentDir);

// Optional: replace a {{main}} tag directly with chosen page
$pageFile = $contentDir . basename($id) . '.html';
if (file_exists($pageFile)) {
    $resolved = str_replace('{{main}}', file_get_contents($pageFile), $resolved);
} else {
    $resolved = str_replace('{{main}}', "<p>Page not found.</p>", $resolved);
}

// Output final HTML
header('Content-Type: text/html; charset=utf-8');
echo $resolved;
