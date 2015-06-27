<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 8/06/15
 * Time: 10:44 AM
 */

namespace Althingi\Lib\Http;

use Zend\Http\PhpEnvironment\Request;

class MultiPart
{
    public function parse(Request $request)
    {
        if ($request->getPost()->count() != 0) {
            return $request->getPost()->toArray();
        }

        /** @var  $contentType \Zend\Http\Header\ContentType */
        if (($contentType = $request->getHeaders('content-type'))) {
            if ($contentType->getMediaType() === 'multipart/form-data') {
                $params = $contentType->getParameters();
                if (array_key_exists('boundary', $params)) {
                    return $this->parseContent($request->getContent(), $params['boundary']);
                }
                return [];
            }
        }

        $parsedParams = [];
        parse_str($request->getContent(), $parsedParams);
        return $parsedParams;
    }

    private function parseContent($content, $boundary)
    {
        $a_data = [];
        // split content by boundary and get rid of last -- element
        $a_blocks = preg_split("/-+$boundary/", $content);
        array_pop($a_blocks);

        // loop data blocks
        foreach ($a_blocks as $id => $block) {
            if (empty($block)) {
                continue;
            }

            // you'll have to var_dump $block to understand this and maybe replace \n or \r with a visibile char

            // parse uploaded files
            if (strpos($block, 'application/octet-stream') !== false) {
                // match "name", then everything after "stream" (optional) except for prepending newlines
                preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
            } else {
                // parse all other fields
                // match "name" and optional value in between newline sequences
                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
            }
            $a_data[$matches[1]] = trim($matches[2]);
        }
        return $a_data;
    }
}
