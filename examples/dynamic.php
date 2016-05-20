<?php
if (!isset($argv[1])) {
    exit('Please provide a url as argument.');
}

$url = $argv[1];
if (false === filter_var($url, FILTER_VALIDATE_URL)) {
    exit("$url is not a valid URL");
}

$rootSelector = null;
if(isset($argv[2]) && is_string($argv[2])) {
    $rootSelector = $argv[2];
}

require_once __DIR__ . '/../vendor/autoload.php';

$content = file_get_contents($url);

$parser = new \Algolia\DOMParser();

$records = $parser->parse($content, $rootSelector);

var_dump($records);
