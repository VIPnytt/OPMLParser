<?php
namespace vipnytt;

use DOMDocument;
use SimpleXMLElement;
use vipnytt\OPMLParser\Exceptions;

class OPMLParser
{
    /**
     * Encoding
     */
    const ENCODING = 'UTF-8';

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
     * XML content
     * @var string
     */
    protected $xml;

    /**
     * Array containing the parsed XML
     * @var array
     */
    protected $result = [];

    /**
     * Constructor
     *
     * @param string $xml is the string we want to parse
     * @throws Exceptions\ParseException
     */
    public function __construct($xml)
    {
        $this->xml = $xml;
        $dom = new DOMDocument();
        $dom->recover = true;
        $dom->strictErrorChecking = false;
        $dom->loadXML($this->xml, LIBXML_NOCDATA);
        $dom->encoding = self::ENCODING;

        $opml = simplexml_import_dom($dom);

        if ($opml === false) {
            throw new Exceptions\ParseException('Provided XML document is not valid');
        }

        $this->result = [
            'version' => (string)$opml['version'],
            'head' => [],
            'body' => []
        ];

        if (!isset($opml->head)) {
            throw new Exceptions\ParseException('Provided XML is not an valid OPML document');
        }
        // First, we get all "head" elements. Head is required but its sub-elements are optional.
        foreach ($opml->head->children() as $key => $value) {
            if (in_array($key, self::OPTIONAL_HEAD_ELEMENTS, true)) {
                $this->result['head'][$key] = (string)$value;
            }
        }
        if (!isset($opml->body)) {
            return;
        }
        // Then, we get body outlines. Body must contain at least one outline element.
        foreach ($opml->body->children() as $key => $value) {
            if ($key === 'outline') {
                $this->result['body'][] = $this->parse_outline($value);
            }
        }
    }

    /**
     * Parse an XML object as an outline object and return corresponding array
     *
     * @param SimpleXMLElement $outline_xml the XML object we want to parse
     * @return array corresponding to an outline and following format described above
     */
    protected function parse_outline(SimpleXMLElement $outline_xml)
    {
        $outline = [];
        foreach ($outline_xml->attributes() as $key => $value) {
            $outline[$key] = (string)$value;
        }
        // Bug fix for OPMLs witch contains `title` but not the required `text`
        if (empty($outline['text']) && isset($outline['title'])) {
            $outline['text'] = $outline['title'];
        }
        foreach ($outline_xml->children() as $key => $value) {
            // An outline may contain any number of outline children
            if ($key === 'outline') {
                $outline['@outlines'][] = $this->parse_outline($value);
            }
        }
        return $outline;
    }

    /**
     * Return the parsed XML as an Array
     *
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Validate the parsed XML array
     * Note: The parser support parsing of OPMLs with missing content
     *
     * @return string|false Validated string on success, false on failure
     */
    public function validate()
    {
        try {
            $render = new OPMLParser\Render($this->result);
        } catch (Exceptions\RenderException $e) {
            return false;
        }
        return $render->asString();
    }
}
