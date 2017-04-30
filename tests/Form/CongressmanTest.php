<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 24/03/2016
 * Time: 4:36 PM
 */

namespace Althingi\Form;

use PHPUnit_Framework_TestCase;

class CongressmanTest extends PHPUnit_Framework_TestCase
{
    public function testEmptyToValue()
    {
        $form = new Congressman();
        $form->setData([
            'congressman_id' => 1,
            'name' => 'Gaur Jonsson',
            'birth' => '2001-01-01',
            'death' => '',
        ])->isValid();

        $this->assertNull($form->getObject()->getDeath());
    }

    public function testNonEmptyToValue()
    {
        $form = new Congressman();
        $form->setData([
            'congressman_id' => 1,
            'name' => 'Gaur Jonsson',
            'birth' => '2001-01-01',
            'death' => '2010-01-01',
        ])->isValid();

        $this->assertNotNull($form->getObject()->getDeath());
    }
}
