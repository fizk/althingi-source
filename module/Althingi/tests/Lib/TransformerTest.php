<?php

namespace AlthingiTest\Lib;

use Althingi\Lib\Transformer;
use PHPUnit\Framework\TestCase;

class TransformerTest extends TestCase
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
