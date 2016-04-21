<?php
namespace vipnytt\OPMLParser;

use DOMDocument;
use SimpleXMLElement;

class Render
{
    /**
     * Encoding to use if not provided
     */
    const ENCODING_DEFAULT = 'UTF-8';

    /**
     * Optional <head> elements
     */
    const OPTIONAL_HEAD_ELEMENTS = [
        'title',
        'dateCreated',
        'dateModified',
        'ownerName',
        'ownerEmail',
        'ownerId',
        'docs',
        'expansionState',
        'vertScrollState',
        'windowTop',
        'windowLeft',
        'windowBottom',
        'windowRight'
    ];

    /**
     * OPML versions supported
     */
    const SUPPORTED_VERSIONS = [
        '2.0',
        '1.0'
    ];

    /**
     * Default OPML version to use, if none is set
     */
    const VERSION_DEFAULT = '2.0';

    /**
     * Version to use for the build
     * 2.0 - `text` attribute is required
     * 1.0 - for legacy support
     * @var bool
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
     * @param string $version '2.0' if `text` attribute is required, '1.0' for legacy
     * @throws Exceptions\RenderException
     */
    public function __construct($array, $version = self::VERSION_DEFAULT)
    {
        $this->version = $version;
        if (!in_array($this->version, self::SUPPORTED_VERSIONS)) {
            throw new Exceptions\RenderException('OPML version `' . $this->version . '` not supported');
        }
        $opml = new SimpleXMLElement('<opml></opml>');
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
            $this->render_outline($body, $outline);
        }
        $this->object = $opml;
    }

    /**
     * Create a XML outline object in a parent object.
     *
     * @param SimpleXMLElement $parent_elt is the parent object of current outline
     * @param array $outline array representing an outline object
     * @return void
     * @throws Exceptions\RenderException
     */
    protected function render_outline($parent_elt, $outline)
    {
        $outline_elt = $parent_elt->addChild('outline');
        $text_is_present = false;
        foreach ($outline as $key => $value) {
            // Only outlines can be an array and so we consider children are also outline elements.
            if ($key === '@outlines' && is_array($value)) {
                foreach ($value as $outline_child) {
                    $this->render_outline($outline_elt, $outline_child);
                }
            } elseif (is_array($value)) {
                throw new Exceptions\RenderException('Type of outline elements cannot be array: ' . $key);
            } else {
                // Detect text attribute is present, that's good :)
                if ($key === 'text') {
                    $text_is_present = true;
                }

                $outline_elt->addAttribute($key, $value);
            }
        }
        if (!$text_is_present && $this->version == '2.0') {
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

    /**
     * Return as an OPML string
     *
     * @param string $encoding Character encoding
     * @return string
     */
    public function asString($encoding = self::ENCODING_DEFAULT)
    {
        $dom = new DOMDocument('1.0', $encoding);
        $dom->loadXML($this->object->asXML());
        $dom->encoding = $encoding;
        $dom->preserveWhiteSpace = false;
        return preg_replace("/\r\n|\n|\r/", '', $dom->saveXML());
    }
}
