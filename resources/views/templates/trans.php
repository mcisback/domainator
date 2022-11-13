<?php

define('DEFAULT_LOCALTE', 'en');

if(!isset($_GET['lang'])) {
    $lang = DEFAULT_LOCALTE;
}

$lang = $_GET['lang'];
$languages = array_map(function($file) {
    return basename(explode('.', $file)[0]);
}, glob(__DIR__ . '/translations/*.json'));

if(!in_array($lang, $languages)) {
    $lang = DEFAULT_LOCALTE;
}

$translations = json_decode(
    file_get_contents(__DIR__ . "/translations/$lang.json"),
    true
);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($translations);
