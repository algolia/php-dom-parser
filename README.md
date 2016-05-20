# What is this repo

A simple way to extract Algolia's search engine friendly records.

This has mainly been built for the Wordpress articles indexing in mind, but the tool is abstracted enough to be re-used on other type of projects.


## Installation

```
$ composer update
$ php examples/simple.php
```

## Examples

### Simple usage

Here is a simple example where we grab the content of an article of Algolia's blog and parse it to obtain the records.

```php
require_once __DIR__ . '/../vendor/autoload.php';

$article = file_get_contents('https://blog.algolia.com/how-we-re-invented-our-office-space-in-paris/');

$parser = new \Algolia\DOMParser();

$records = $parser->parse($article, 'article.post');

var_dump($records);
```

### Little CLI

`dynamic.php` is a little cli for dynamically fetching the dom of some url.
You can optionally pass a root selector as second argument.

```
$ php examples/dynamic.php https://blog.algolia.com/inside-the-algolia-engine-part-2-the-indexing-challenge-of-instant-search/ article.post
```

## Dev

Test the code.
```
vendor/bin/phpunit
```

