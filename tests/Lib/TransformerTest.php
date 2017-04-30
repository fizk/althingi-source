<?php
/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 4/6/17
 * Time: 8:47 AM
 */

namespace Althingi\Lib;

use PHPUnit_Framework_TestCase;

class TransformerTest extends PHPUnit_Framework_TestCase
{

    public function htmlToMarkdownData()
    {
        return [
            [null, null],
            ['', null],
            ['<h1>Hallo</h1>', "Hallo\n====="],
        ];
    }

    /**
     * @dataProvider htmlToMarkdownData
     * @param mixed $input
     * @param mixed $output
     */
    public function testHtmlToMarkdown($input, $output)
    {
        $this->assertEquals($output, Transformer::htmlToMarkdown($input));
    }

    public function speechToMarkdownData()
    {
        return [
            [null, ''],
            [false, ''],
            [true, ''],
        ];
    }

    /**
     * @dataProvider speechToMarkdownData
     * @param mixed $input
     * @param mixed $output
     */
    public function testSpeechToMarkdown($input, $output)
    {
        $this->assertEquals($output, Transformer::speechToMarkdown($input));
    }
}
