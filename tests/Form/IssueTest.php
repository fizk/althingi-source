<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 24/03/2016
 * Time: 4:36 PM
 */

namespace Althingi\Form;

use PHPUnit_Framework_TestCase;

class IssueTest extends PHPUnit_Framework_TestCase
{
    public function testObjectGoesThrough()
    {
        $inputData = [
            'issue_id' => 1,
            'assembly_id' => 1,
            'category' => 'cat',
            'name' => 'nam',
            'type' => 'typ',
            'type_name' => 'typ-nam',
            'type_subname' => 'sub',
            'status' => 'some',
        ];

        $form = new Issue();
        $form->setData($inputData)->isValid();
        $object = $form->getObject();

        $this->assertEquals((object) $inputData, $object);
    }

}
