<?php
namespace vipnytt\OPMLParser\Tests;

use vipnytt\OPMLParser;

/**
 * Class ParseTest
 *
 * @package vipnytt\OPMLParser\Tests
 */
class ParseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $xml
     * @param array $result
     */
    public function testParse($xml, $result)
    {
        $parser = new OPMLParser($xml);
        $this->assertInstanceOf('vipnytt\OPMLParser', $parser);

        $this->assertTrue(is_array($parser->getResult()));
        $this->assertEquals($parser->getResult(), $result);
    }

    /**
     * Generate test data
     * @return array
     */
    public function generateDataForTest()
    {
        return [
            [
                <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<opml version="2.0">
    <head>
        <title>OPMLv2 Head title</title>
    </head>
    <body>
        <outline title="Title 1" text="Text 1" type="rss" xmlUrl="http://example.com/page1.xml"
                 htmlUrl="http://example.com/page1.html"/>
        <outline title="Title 2" text="Text 2" type="rss" xmlUrl="http://example.com/page2.xml"
                 htmlUrl="http://example.com/page2.html"/>
        <outline title="Title 3" text="Text 3" type="rss" xmlUrl="http://example.com/page3.xml"
                 htmlUrl="http://example.com/page3.html"/>
    </body>
</opml>
XML
                ,
                [
                    'version' => '2.0',
                    'head' => [
                        'title' => 'OPMLv2 Head title'
                    ],
                    'body' => [
                        [
                            'title' => 'Title 1',
                            'text' => 'Text 1',
                            'type' => 'rss',
                            'xmlUrl' => 'http://example.com/page1.xml',
                            'htmlUrl' => 'http://example.com/page1.html',
                        ],
                        [
                            'title' => 'Title 2',
                            'text' => 'Text 2',
                            'type' => 'rss',
                            'xmlUrl' => 'http://example.com/page2.xml',
                            'htmlUrl' => 'http://example.com/page2.html',
                        ],
                        [
                            'title' => 'Title 3',
                            'text' => 'Text 3',
                            'type' => 'rss',
                            'xmlUrl' => 'http://example.com/page3.xml',
                            'htmlUrl' => 'http://example.com/page3.html',
                        ],
                    ]
                ]
            ]
        ];
    }
}
