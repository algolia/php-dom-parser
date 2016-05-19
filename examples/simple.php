<?php
require_once '../vendor/autoload.php';

$article = file_get_contents('article.html');

$parser = new \Algolia\DOMParser();

$records = $parser->parse($article);

var_dump($records);


