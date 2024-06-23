<?php

namespace Althingi\Form;

use Althingi\Form\Congressman;
use PHPUnit\Framework\TestCase;

class CongressmanTest extends TestCase
{
    public function testEmptyToValue()
    {
        $form = new Congressman([
            'congressman_id' => 1,
            'name' => 'Gaur Jonsson',
            'birth' => '2001-01-01',
            'death' => '',

        ]);
        $form->isValid();

        /** @var \Althingi\Model\Congressman */
        $model = $form->getModel();
        $this->assertNull($model->getDeath());
    }

    public function testNonEmptyToValue()
    {
        $form = new Congressman([
            'congressman_id' => 1,
            'name' => 'Gaur Jonsson',
            'birth' => '2001-01-01',
            'death' => '2010-01-01',
        ]);
        $form->isValid();

        /** @var \Althingi\Model\Congressman */
        $model = $form->getModel();
        $this->assertNotNull($model->getDeath());
    }
}
