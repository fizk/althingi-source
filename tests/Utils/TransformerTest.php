<?php

namespace Althingi\Lib;

use Althingi\Utils\Transformer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TransformerTest extends TestCase
{
    public static function htmlToMarkdownData()
    {
        return [
            [null, null],
            ['', null],
            ['<h1>Hallo</h1>', "Hallo\n====="],
        ];
    }

    #[DataProvider('htmlToMarkdownData')]
    public function testHtmlToMarkdown($input, $output)
    {
        $this->assertEquals($output, Transformer::htmlToMarkdown($input));
    }

    public static function speechToMarkdownData()
    {
        return [
            [null, ''],
            [false, ''],
            [true, ''],
        ];
    }

    #[DataProvider('speechToMarkdownData')]
    public function testSpeechToMarkdown($input, $output)
    {
        $this->assertEquals($output, Transformer::speechToMarkdown($input));
    }
}
