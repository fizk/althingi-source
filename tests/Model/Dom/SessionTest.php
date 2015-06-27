<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 9/06/15
 * Time: 7:19 PM
 */

namespace Althingi\Model\Dom;

use \PHPUnit_Framework_TestCase;

class SessionTest extends PHPUnit_Framework_TestCase
{
    public function testAllElements()
    {
        $dom = new \DOMDocument();
        $root = $dom->createElement('whatever');

        $in = $dom->createElement('inn');
        $in->appendChild($dom->createTextNode('10.02.2002'));
        $root->appendChild($in);

        $model = new Session();
        $result = $model->extract($root);

        $this->assertEquals('2002-02-10', $result['from']);

    }

    /**
     * @expectedException \Althingi\Model\Exception
     * @throws \Althingi\Model\Exception
     */
    public function testMissingFromDate()
    {
        $dom = new \DOMDocument();
        $root = $dom->createElement('whatever');

        $model = new Session();
        $model->extract($root);
    }

    public function testDifferentDateFormat()
    {
        $dom = new \DOMDocument();
        $root = $dom->createElement('whatever');

        $in = $dom->createElement('inn');
        $in->appendChild($dom->createTextNode('20.10.2014'));
        $root->appendChild($in);

        $model = new Session();
        $result = $model->extract($root);

        $this->assertEquals('2014-10-20', $result['from']);

    }
}
