<?php

if(count($argv) <= 1) {
    die('Missing template directory name');
}

$template = $argv[1];
$indexFilename = $argv[2] ?? 'index.blade.php';

$templatePath = __DIR__ . "/resources/views/templates/$template/$indexFilename";

if(!file_exists($templatePath)) {
    die("Template '$templatePath' does not exist");
}

$templateHtml = file_get_contents($templatePath);

if(($count = preg_match_all('/\{\{\s*\$([^\}\s]+)\s*\}\}/', $templateHtml, $matches))) {
    $data = [];

    foreach($matches[1] as $match) {
        $data[$match] = "string";
    }

    if(($count = preg_match_all('/\:[^="]+="\$([^"]+)"/', $templateHtml, $compVars))) {
        foreach($compVars[1] as $match) {
            $data[$match] = "component";
        }
    }

    echo json_encode($data, JSON_PRETTY_PRINT);

} else {
    die("No blade vars found");
}
