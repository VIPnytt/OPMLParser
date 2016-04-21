<?php
namespace vipnytt\OPMLParser;

use SimpleXMLElement;

class Render implements OPMLInterface
{
    /**
     * Version to use for the build
     * 2.0 - `text` attribute is required
     * 1.0 - for legacy support
     * @var string
     */
    protected $version;

    /**
     * Rendered XML object
     * @var SimpleXMLElement
     */
    protected $object;

    /**
     * Constructor
     *
     * @param array $array is the array we want to render and must follow structure defined above
     * @param string $encoding character encoding to use
     * @param string $version '2.0' if `text` attribute is required, '1.0' for legacy
     * @throws Exceptions\RenderException
     */
    public function __construct($array, $encoding = self::ENCODING, $version = self::VERSION_DEFAULT)
    {
        $this->version = $version;
        if (!in_array($this->version, self::SUPPORTED_VERSIONS)) {
            throw new Exceptions\RenderException('OPML version `' . $this->version . '` not supported');
        }
        $opml = new SimpleXMLElement('<?xml version="1.0" encoding="' . $encoding . '"?><opml></opml>');
        $opml->addAttribute('version', (string)$this->version);
        // Create head element. It is optional but head element will exist in the final XML object.
        $head = $opml->addChild('head');
        if (isset($array['head'])) {
            foreach ($array['head'] as $key => $value) {
                if (in_array($key, self::OPTIONAL_HEAD_ELEMENTS, true)) {
                    $head->addChild($key, $value);
                }
            }
        }
        // Check body is set and contains at least one element
        if (!isset($array['body'])) {
            throw new Exceptions\RenderException('The body element is missing');
        }
        // Create outline elements
        $body = $opml->addChild('body');
        foreach ($array['body'] as $outline) {
            $this->renderOutline($body, $outline);
        }
        $this->object = $opml;
    }

    /**
     * Create a XML outline object in a parent object.
     *
     * @param SimpleXMLElement $parent is the parent object of current outline
     * @param array $outline array representing an outline object
     * @return void
     * @throws Exceptions\RenderException
     */
    protected function renderOutline($parent, $outline)
    {
        $outlineSub = $parent->addChild('outline');
        $textIsPresent = false;
        foreach ($outline as $key => $value) {
            // Only outlines can be an array and so we consider children are also outline elements.
            if ($key === '@outlines' && is_array($value)) {
                foreach ($value as $outlineChild) {
                    $this->renderOutline($outlineSub, $outlineChild);
                }
            } elseif (is_array($value)) {
                throw new Exceptions\RenderException('Type of outline elements cannot be array: ' . $key);
            } else {
                // Detect text attribute is present, that's good :)
                if ($key === 'text') {
                    $textIsPresent = true;
                }

                $outlineSub->addAttribute($key, $value);
            }
        }
        if (!$textIsPresent && $this->version == '2.0') {
            throw new Exceptions\RenderException('The text element must be present for all outlines (applies to version 2.0 only)');
        }
    }

    /**
     * Return as a XML object
     *
     * @return SimpleXMLElement
     */
    public function asXMLObject()
    {
        return $this->object;
    }
}
