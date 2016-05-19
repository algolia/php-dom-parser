<?php

namespace Algolia;

class DOMParser
{
    /**
     * An array of attributeName => domSelector.
     * Order matters and will determine the hierarchy of the page.
     * Only tags are supported for now.
     *
     * @var array
     */
    private $attributes = array(
        'h1'      => 'h1',
        'h2'      => 'h2',
        'h3'      => 'h3',
        'h4'      => 'h4',
        'h5'      => 'h5',
        'h6'      => 'h6',
        'content' => 'p, ul, ol, dl, table',
    );

    /**
     * All content in the listed selector will not pe parsed at all.
     * //todo: use this.
     *
     * @var array
     */
    private $ignored = array(
        'pre',
        'script',
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
     * Algolia_DOM_Parser constructor.
     */
    public function __construct(array $attributes = array())
    {
        if (!empty($attributes)) {
            $this->attributes = $attributes;
        }
        $this->currentObject = $this->getNewEmptyObject();
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
     * @param string $dom
     *
     * @return array
     */
    public function parse($dom)
    {
        $globalSelector = implode(',', $this->attributes);
        $dom = new \simple_html_dom((string) $dom);

        $nodes = $dom->find($globalSelector);
        foreach ($nodes as $node) {
            /* @var \simple_html_dom_node $node */
            $attributeKey = $this->getMatchingAttributeKey($node);
            $level = $this->getAttributeLevel($attributeKey);

            if ($level <= $this->currentLevel) {
                $this->publishCurrentObject();
                $this->prepareCurrentObject($level);
            }

            $this->setCurrentObjectAttribute($attributeKey, $node);
            $this->currentLevel = $level;
        }
        $this->publishCurrentObject();

        return $this->parsedObjects;
    }

    /**
     * @param string                $attributeKey
     * @param \simple_html_dom_node $node
     */
    private function setCurrentObjectAttribute($attributeKey, \simple_html_dom_node $node)
    {
        $text = $node->innertext();
        $text = strip_tags($text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        $this->currentObject[$attributeKey] = $text;
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
