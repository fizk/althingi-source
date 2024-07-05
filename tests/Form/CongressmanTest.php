<?php

namespace Althingi\Form;

use Althingi\Form\Congressman;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CongressmanTest extends TestCase
{
    #[Test]
    public function emptyValuesAreConvertedToNULL()
    {
        $form = new Congressman([
            'congressman_id' => 1,
            'name' => 'Gaur Jonsson',
            'birth' => '2001-01-01',
            'death' => '',

        ]);
        $form->isValid();

        $model = $form->getModel();
        $this->assertNull($model->getDeath());
    }

    #[Test]
    public function nonEmptyValuesShouldNotBeConvertedToNULL()
    {
        $form = new Congressman([
            'congressman_id' => 1,
            'name' => 'Gaur Jonsson',
            'birth' => '2001-01-01',
            'death' => '2010-01-01',
        ]);
        $form->isValid();

        $model = $form->getModel();
        $this->assertNotNull($model->getDeath());
    }
}
