<?php

namespace Althingi\Lib;

use Althingi\Utils\Transformer;
use PHPUnit\Framework\Attributes\{DataProvider, Test};
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

    #[Test]
    #[DataProvider('htmlToMarkdownData')]
    public function htmlToMarkdown($input, $output)
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

    #[Test]
    #[DataProvider('speechToMarkdownData')]
    public function speechToMarkdown($input, $output)
    {
        $this->assertEquals($output, Transformer::speechToMarkdown($input));
    }
}
