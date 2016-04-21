<?php
namespace vipnytt\OPMLParser\Tests;

use vipnytt\OPMLParser\Render;

/**
 * Class RenderTest
 *
 * @package vipnytt\OPMLParser\Tests
 */
class RenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param array $array
     * @param string $result
     */
    public function testRender($array, $result)
    {
        $parser = new Render($array);
        $this->assertInstanceOf('vipnytt\OPMLParser\Render', $parser);
        $this->assertEquals($result, $parser->asString(false));
    }

    /**
     * Generate test data
     * @return array
     */
    public function generateDataForTest()
    {
        return [
            [
                [
                    'body' => [
                        [
                            'text' => 'test'
                        ]
                    ]
                ],
                <<<XML
<?xml version="1.0" encoding="UTF-8"?><opml version="2.0"><head/><body><outline text="test"/></body></opml>
XML
            ]
        ];
    }
}
