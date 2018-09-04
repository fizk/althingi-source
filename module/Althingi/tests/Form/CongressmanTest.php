<?php

namespace AlthingiTest\Form;

use Althingi\Form\Congressman;
use PHPUnit\Framework\TestCase;

class CongressmanTest extends TestCase
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
