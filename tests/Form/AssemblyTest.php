<?php

namespace Althingi\Form;

use Althingi\Form\Assembly;
use PHPUnit\Framework\TestCase;

class AssemblyTest extends TestCase
{
    public function testEmptyToValue()
    {
        $form = new Assembly();
        $form->setData([
            'assembly_id' => 1,
            'from' => '2001-02-01',
            'to' => '',
        ])->isValid();

        /** @var \Althingi\Model\Assembly */
        $model = $form->getObject();
        $this->assertNull($model->getTo());
    }

    public function testNonEmptyToValue()
    {
        $form = new Assembly();
        $form->setData([
            'assembly_id' => 1,
            'from' => '2001-02-01',
            'to' => '2001-02-02',
        ])->isValid();

        /** @var \Althingi\Model\Assembly */
        $model = $form->getObject();
        $this->assertNotNull($model->getTo());
    }
}
