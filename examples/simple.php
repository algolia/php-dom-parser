<?php

require_once __DIR__ . '/../vendor/autoload.php';

$article = file_get_contents('https://blog.algolia.com/how-we-re-invented-our-office-space-in-paris/');

$parser = new \Algolia\DOMParser();

$records = $parser->parse($article, 'article.post');

var_dump($records);
