<?php

namespace Althingi\Form;

use Althingi\Form\Assembly;
use Althingi\Form\Constituency;
use PHPUnit\Framework\TestCase;

class ConstituencyTest extends TestCase
{
    public function testNullName()
    {
        $form = new Constituency();
        $form->setData([
            'constituency_id' => 1,
            'name' => null,
            'abbr_short' => null,
            'abbr_long' => null,
            'description' => null,
        ])->isValid();

        /** @var \Althingi\Model\Constituency */
        $model = $form->getObject();
        $this->assertEquals('-', $model->getName());
    }

    public function testEmptyName()
    {
        $form = new Constituency();
        $form->setData([
            'constituency_id' => 1,
            'name' => '',
            'abbr_short' => null,
            'abbr_long' => null,
            'description' => null,
        ])->isValid();

        /** @var \Althingi\Model\Constituency */
        $model = $form->getObject();
        $this->assertEquals('-', $model->getName());
    }
}
