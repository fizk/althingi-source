<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 24/03/2016
 * Time: 4:36 PM
 */

namespace Althingi\Form;

use PHPUnit_Framework_TestCase;

class VoteTest extends PHPUnit_Framework_TestCase
{
    public function testObjectGoesThrough()
    {
        $inputData = [
            'issue_id' => 1,
            'assembly_id' => 1,
            'vote_id' => 1,
            'date' => '2001-01-01 01:01:00',
            'type' => 'hundur',
            'outcome' => 'samtykkt',
            'method' => 'method',
            'yes' => 1,
            'no' => 1,
            'inaction' => 1,
        ];

        $form = new Vote();
        $form->setData($inputData)->isValid();
        $object = $form->getObject();

        $this->assertEquals((object) $inputData, $object);
    }

    public function testAnotherDate()
    {
        $inputData = [
            'issue_id' => 1,
            'assembly_id' => 1,
            'vote_id' => 1,
            'date' => '2015-12-16 23:42:27',
            'type' => 'hundur',
            'outcome' => 'samtykkt',
            'method' => 'method',
            'yes' => 1,
            'no' => 1,
            'inaction' => 1,
        ];

        $form = new Vote();
        $form->setData($inputData)->isValid();
        $object = $form->getObject();

        $this->assertEquals((object) $inputData, $object);
    }
}
