<?php

namespace Althingi\Form;

use Althingi\Form\Assembly;
use PHPUnit\Framework\TestCase;

class AssemblyTest extends TestCase
{
    public function testEmptyToValue()
    {
        $form = (new Assembly([
            'assembly_id' => 1,
            'from' => '2001-02-01',
            'to' => '',

        ]));
        $form->isValid();

        /** @var \Althingi\Model\Assembly */
        $model = $form->getModel();
        $this->assertNull($model->getTo());
    }

    public function testNonEmptyToValue()
    {
        $form = new Assembly([
            'assembly_id' => 1,
            'from' => '2001-02-01',
            'to' => '2001-02-02',
        ]);
        $form->isValid();

        /** @var \Althingi\Model\Assembly */
        $model = $form->getModel();
        $this->assertNotNull($model->getTo());
    }
}
