<?php

namespace Althingi\Form;

use Althingi\Form\Plenary;
use PHPUnit\Framework\TestCase;

class PlenaryTest extends TestCase
{
    public function testEmptyToValue()
    {
        $form = new Plenary();
        $isValid = $form->setData([
            'plenary_id' => '1',
            'assembly_id' => '2',
            'name' => '',
            'from' => '',
            'to' => '',
        ])->isValid();

        $this->assertTrue($isValid);
    }

    public function testNegativeValue()
    {
        $form = new Plenary();
        $isValid = $form->setData([
            'plenary_id' => '-1',
            'assembly_id' => '2',
            'name' => '',
            'from' => '',
            'to' => '',
        ])->isValid();

        $this->assertTrue($isValid);
    }

}
