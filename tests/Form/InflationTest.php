<?php

namespace Althingi\Form;

use Althingi\{Form, Model};
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use DateTime;

class InflationTest extends TestCase
{
    #[Test]
    public function stringValueIsConvertedIntoFloadAndDateToObject()
    {
        $form = new Form\Inflation([
            'id' => 1,
            'date' => '2001-02-01',
            'value' => '1.2',

        ]);
        $form->isValid();

        $actual = $form->getModel();
        $expected = (new Model\Inflation())
            ->setDate(new DateTime('2001-02-01'))
            ->setId(1)
            ->setValue(1.2);
        $this->assertEquals($expected, $actual);
    }

    #[Test]
    public function floatValueIsLeftAloneAndDateIsConvertedIntoObject()
    {
        $form = new Form\Inflation([
            'id' => 1,
            'date' => '2001-02-01',
            'value' => 1.2,

        ]);
        $form->isValid();

        $actual = $form->getModel();
        $expected = (new Model\Inflation())
            ->setDate(new DateTime('2001-02-01'))
            ->setId(1)
            ->setValue(1.2);
        $this->assertEquals($expected, $actual);
    }

    #[Test]
    public function intIsConvertedIntoFloadAndDateToObject()
    {
        $form = new Form\Inflation([
            'id' => 1,
            'date' => '2001-02-01',
            'value' => 1,
        ]);
        $form->isValid();

        $actual = $form->getModel();
        $expected = (new Model\Inflation())
            ->setDate(new DateTime('2001-02-01'))
            ->setId(1)
            ->setValue(1.0);
        $this->assertEquals($expected, $actual);
    }

    #[Test]
    public function intStringIsConvertedIntoFloadAndDateConvertedToObject()
    {
        $form = new Form\Inflation([
            'id' => 1,
            'date' => '2001-02-01',
            'value' => '1',
        ]);
        $form->isValid();

        $actual = $form->getModel();
        $expected = (new Model\Inflation())
            ->setDate(new DateTime('2001-02-01'))
            ->setId(1)
            ->setValue(1.0);
        $this->assertEquals($expected, $actual);
    }
}
