<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 24/03/2016
 * Time: 4:36 PM
 */

namespace Althingi\Form;

use PHPUnit_Framework_TestCase;

class SpeechTest extends PHPUnit_Framework_TestCase
{
    public function testObjectGoesThrough()
    {
        $inputData = [
            'speech_id' => 1,
            'to' => '2001-01-01 00:00:00',
            'from' => '2001-01-01 00:00:00',
            'plenary_id' => 1,
            'assembly_id' => 1,
            'issue_id' => 1,
            'congressman_id' => 1,
            'congressman_type' => 'gaur',
            'iteration' => '1',
            'type' => 'stuff',
            'text' => 'fluff',
        ];

        $form = new Speech();
        $form->setData($inputData)->isValid();
        $object = $form->getObject();

        $this->assertEquals((object) $inputData, $object);
    }
}
