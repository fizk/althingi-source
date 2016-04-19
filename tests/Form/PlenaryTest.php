<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 24/03/2016
 * Time: 4:36 PM
 */

namespace Althingi\Form;

use PHPUnit_Framework_TestCase;

class PlenaryTest extends PHPUnit_Framework_TestCase
{
    public function testObjectGoesThrough()
    {
        $inputData = [
            'plenary_id' => 1,
            'assembly_id' => 1,
            'name' => 'some-name',
            'from' => '2001-01-01 00:00',
            'to' => '2001-01-01 00:00',
        ];

        $form = new Plenary();
        $form->setData($inputData)->isValid();
        $object = $form->getObject();

        $this->assertEquals((object) $inputData, $object);
    }

    public function testDateOne()
    {
        $inputData = [
            'plenary_id' => 1,
            'assembly_id' => 1,
            'name' => 'some-name',
            'from' => '2001-01-01 00:00',
            'to' => '2001-01-01 00:00',
        ];

        $form = new Plenary();
        $form->setData($inputData)->isValid();
        $object = $form->getObject();


        array_map(function ($element) {
            /** @var $element \Zend\Form\ElementInterface */
            print_r($element->getMessages());
        }, $form->getElements());

        $this->assertEquals((object) $inputData, $object);
    }
}
