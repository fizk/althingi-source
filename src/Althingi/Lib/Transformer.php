<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 1/04/2016
 * Time: 2:25 PM
 */

namespace Althingi\Lib;

class Transformer
{
    public static function speechToMarkdown($text)
    {
        $text =  preg_replace('/(<frammíkall.*?>)(.*?)(<\/frammíkall>)/i', "**[frammíkall: $2]**", $text);

        $dom = new \DOMDocument();
        $dom->loadXML($text);

        $paragraphs = array_map(function ($paragraph) {
            return trim($paragraph->nodeValue);
        }, iterator_to_array($dom->getElementsByTagName('mgr')));

        return implode("\n\n", $paragraphs);
    }
}
