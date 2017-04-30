<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 1/04/2016
 * Time: 2:25 PM
 */

namespace Althingi\Lib;

use League\HTMLToMarkdown\HtmlConverter;

class Transformer
{
    public static function speechToMarkdown($text)
    {
        if (empty($text)) {
            return '';
        }
        
        $text =  preg_replace('/(<frammíkall.*?>)(.*?)(<\/frammíkall>)/i', "**[frammíkall: $2]**", $text);

        $dom = new \DOMDocument();
        if (@$dom->loadXML($text) == true) {
            $paragraphs = array_map(function ($paragraph) {
                return trim($paragraph->nodeValue);
            }, iterator_to_array($dom->getElementsByTagName('mgr')));

            return implode("\n\n", $paragraphs);
        }
        return $text;
    }

    public static function htmlToMarkdown($html)
    {
        if (!$html) {
            return null;
        }
        $converter = new HtmlConverter(['strip_tags' => true]);
        return $converter->convert($html);
    }
}
