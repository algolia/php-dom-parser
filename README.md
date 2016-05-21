# What is this repo

A simple tool to turn DOM into Algolia friendly records.

It has been built with Wordpress articles indexing in mind,
but the tool is now abstracted enough to be re-used on other type of projects.

For now the parsed DOM will result in the minimum possible number of records, meaning that if a node
has at least one child, it will never have a record on its own. If we need such a behaviour, we could easily add it.


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

// Exclude content by CSS selectors.
$parser->setExcludeSelectors(array(
    'pre',
    '.entry-meta',
    'div.rp4wp-related-posts'
));

// Only parse what is inside a given CSS selectors.
// If there are multiple nodes matching, they will all be parsed.
$parser->setRootSelector('article.post');

// Define your attributes sibling.
$parser->setAttributeSelectors(
	array(
        'title1'  => 'h1',
        'title2'  => 'h2',
        'title3'  => 'h3',
        'title4'  => 'h4',
        'title5'  => 'h5',
        'title6'  => 'h6',
        'content' => 'p, ul, ol, dl, table',
    )
);

// Turn the DOM into Algolia search friendly records.
$records = $parser->parse($article);

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

