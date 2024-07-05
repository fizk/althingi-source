<?php

namespace Althingi\Form;

use Althingi\Form\Constituency;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ConstituencyTest extends TestCase
{
    #[Test]
    public function NULLNamesAreConvertedIntoDash()
    {
        $form = new Constituency([
            'constituency_id' => 1,
            'name' => null,
            'abbr_short' => null,
            'abbr_long' => null,
            'description' => null,
        ]);
        $form->isValid();

        $model = $form->getModel();
        $this->assertEquals('-', $model->getName());
    }

    #[Test]
    public function emptyNamesAreConvertedIntoDash()
    {
        $form = new Constituency([
            'constituency_id' => 1,
            'name' => '',
            'abbr_short' => null,
            'abbr_long' => null,
            'description' => null,
        ]);
        $form->isValid();

        $model = $form->getModel();
        $this->assertEquals('-', $model->getName());
    }
}
