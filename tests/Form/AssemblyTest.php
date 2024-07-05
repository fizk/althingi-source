<?php

namespace Althingi\Form;

use Althingi\Form\Assembly;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AssemblyTest extends TestCase
{
    #[Test]
    public function emptyShouldBeConvertedToNULL()
    {
        $form = (new Assembly([
            'assembly_id' => 1,
            'from' => '2001-02-01',
            'to' => '',

        ]));
        $form->isValid();

        $model = $form->getModel();
        $this->assertNull($model->getTo());
    }

    #[Test]
    public function nonEmptyShouldBeLeftAlone()
    {
        $form = new Assembly([
            'assembly_id' => 1,
            'from' => '2001-02-01',
            'to' => '2001-02-02',
        ]);
        $form->isValid();

        $model = $form->getModel();
        $this->assertNotNull($model->getTo());
    }
}
