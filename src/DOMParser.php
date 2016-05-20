<?php

namespace Algolia;

final class DOMParser
{
    /**
     * An array of attributeName => domSelector.
     * Order matters and will determine the hierarchy of the page.
     * Only tags are supported for now.
     *
     * @var array
     */
    private $attributes = array(
        'title1'  => 'h1',
        'title2'  => 'h2',
        'title3'  => 'h3',
        'title4'  => 'h4',
        'title5'  => 'h5',
        'title6'  => 'h6',
        'content' => 'p, ul, ol, dl, table',
    );

    /**
     * Keeps track of the current depth in the hierarchy during parsing.
     *
     * @var int
     */
    private $currentLevel = -1;

    /**
     * @var array
     */
    private $parsedObjects = array();

    /**
     * @var array
     */
    private $currentObject = array();

    /**
     * All content in the listed selector will not pe parsed at all.
     *
     * @var array
     */
    private $exclude = array(
        'pre',
        'script',
    );

    /**
     * Algolia_DOM_Parser constructor.
     *
     * @param array      $attributes
     * @param array|null $exclude
     */
    public function __construct(array $attributes = array(), array $exclude = null)
    {
        if (!empty($attributes)) {
            $this->attributes = $attributes;
        }
        $this->currentObject = $this->getNewEmptyObject();

        if (is_array($exclude)) {
            $this->exclude = $exclude;
        }
    }

    /**
     * @return array
     */
    private function getNewEmptyObject()
    {
        $object = array();
        foreach ($this->attributes as $attributeKey => $selector) {
            $object[$attributeKey] = '';
        }

        return $object;
    }

    /**
     * @param int $forAttributeLeveled
     */
    private function prepareCurrentObject($forAttributeLeveled)
    {
        $new = $this->getNewEmptyObject();
        if (0 === $forAttributeLeveled) {
            // We are at the root, no need to do copy anything.
            $this->currentObject = $new;

            return;
        }

        $counter = 0;
        foreach ($this->attributes as $attributeKey => $selector) {
            // We copy the values till we reached the expected level.
            $new[$attributeKey] = $this->currentObject[$attributeKey];

            if (++$counter === $forAttributeLeveled) {
                break;
            }
        }
        $this->currentObject = $new;
    }

    /**
     * @param string      $dom
     * @param string|null $rootSelector
     *
     * @return array
     */
    public function parse($dom, $rootSelector = null)
    {
        $dom = new \simple_html_dom((string) $dom);

        if (is_string($rootSelector)) {
            /* @var \simple_html_dom_node $dom */
            $rootNodes = $dom->find($rootSelector);
            if (empty($dom)) {
                return array();
            }
        } else {
            $rootNodes = array($dom);
        }

        foreach ($rootNodes as $rootNode) {
            $this->parseNode($rootNode);
        }

        return $this->parsedObjects;
    }

    private function parseNode($rootNode)
    {
        $globalSelector = implode(',', $this->attributes);
        $nodes = $rootNode->find($globalSelector);
        foreach ($nodes as $node) {
            /* @var \simple_html_dom_node $node */
            $attributeKey = $this->getMatchingAttributeKey($node);
            $level = $this->getAttributeLevel($attributeKey);

            $attributeValue = $this->getAttributeValue($node);
            if (empty($attributeValue)) {
                // We skip empty values to not add ghost records.
                continue;
            }

            // If we are deeper in the hierarchy, we need to create a record and go up to
            // the current element level.
            if ($level <= $this->currentLevel) {
                $this->publishCurrentObject();
                $this->prepareCurrentObject($level);
            }

            $this->setCurrentObjectAttribute($attributeKey, $attributeValue);
            $this->currentLevel = $level;
        }
        $this->publishCurrentObject();

        return $this->parsedObjects;
    }

    /**
     * @param \simple_html_dom_node $node
     *
     * @return mixed|string
     */
    private function getAttributeValue(\simple_html_dom_node $node)
    {
        // Remove excluded content.
        $excludeSelector = implode(',', $this->exclude);
        $excludedNodes = $node->find($excludeSelector);
        foreach ($excludedNodes as $excludedNode) {
            $excludedNode->outertext = '';
        }

        // Prepare text output.
        $text = $node->innertext();
        $text = strip_tags($text);
        $text = str_replace('&nbsp;', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        return $text;
    }

    /**
     * @param string $attributeKey
     * @param string $attributeValue
     */
    private function setCurrentObjectAttribute($attributeKey, $attributeValue)
    {
        $this->currentObject[$attributeKey] = $attributeValue;
    }

    /**
     * @param string $attributeKey
     *
     * @return int
     */
    private function getAttributeLevel($attributeKey)
    {
        if (!isset($this->attributes[$attributeKey])) {
            throw new \InvalidArgumentException(sprintf('No attribute is keyed %s.', $attributeKey));
        }
        $keys = array_keys($this->attributes);

        return array_search($attributeKey, $keys);
    }

    /**
     * @param \simple_html_dom_node $node
     *
     * @return int|string
     */
    private function getMatchingAttributeKey(\simple_html_dom_node $node)
    {
        $tag = $node->tag;
        foreach ($this->attributes as $attributeKey => $selector) {
            $selector = str_replace(' ', '', $selector);
            $selectorTags = explode(',', $selector);
            if (in_array($tag, $selectorTags)) {
                return $attributeKey;
            }
        }

        throw new \RuntimeException(sprintf('Tag %s does not match any attribute.', $tag));
    }

    private function publishCurrentObject()
    {
        $this->parsedObjects[] = $this->currentObject;
    }
}
