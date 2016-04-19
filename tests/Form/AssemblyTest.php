<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 24/03/2016
 * Time: 4:36 PM
 */

namespace Althingi\Form;

use PHPUnit_Framework_TestCase;

class AssemblyTest extends PHPUnit_Framework_TestCase
{
    public function testEmptyToValue()
    {
        $form = new Assembly();
        $form->setData([
            'assembly_id' => 1,
            'from' => '2001-02-01',
            'to' => '',
        ])->isValid();

        $this->assertNull($form->getObject()->to);
    }

    public function testNonEmptyToValue()
    {
        $form = new Assembly();
        $form->setData([
            'assembly_id' => 1,
            'from' => '2001-02-01',
            'to' => '2001-02-02',
        ])->isValid();

        $this->assertNotNull($form->getObject()->to);
    }
}
