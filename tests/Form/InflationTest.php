<?php

namespace Althingi\Form;

use Althingi\Form\Inflation;
use Althingi\Model\Inflation as ModelInflation;
use PHPUnit\Framework\TestCase;
use DateTime;

class InflationTest extends TestCase
{
    public function testOne()
    {
        $form = new Inflation();
        $form->setData([
            'id' => 1,
            'date' => '2001-02-01',
            'value' => '1.2',
        ])->isValid();

        /** @var \Althingi\Model\Assembly */
        $actual = $form->getObject();
        $expected = (new ModelInflation())
            ->setDate(new DateTime('2001-02-01'))
            ->setId(1)
            ->setValue(1.2);
        $this->assertEquals($expected, $actual);
    }

    public function testTwo()
    {
        $form = new Inflation();
        $form->setData([
            'id' => 1,
            'date' => '2001-02-01',
            'value' => 1.2,
        ])->isValid();

        /** @var \Althingi\Model\Assembly */
        $actual = $form->getObject();
        $expected = (new ModelInflation())
            ->setDate(new DateTime('2001-02-01'))
            ->setId(1)
            ->setValue(1.2);
        $this->assertEquals($expected, $actual);
    }

    public function testThree()
    {
        $form = new Inflation();
        $form->setData([
            'id' => 1,
            'date' => '2001-02-01',
            'value' => 1,
        ])->isValid();

        /** @var \Althingi\Model\Assembly */
        $actual = $form->getObject();
        $expected = (new ModelInflation())
            ->setDate(new DateTime('2001-02-01'))
            ->setId(1)
            ->setValue(1.0);
        $this->assertEquals($expected, $actual);
    }

    public function testFour()
    {
        $form = new Inflation();
        $form->setData([
            'id' => 1,
            'date' => '2001-02-01',
            'value' => '1',
        ])->isValid();

        /** @var \Althingi\Model\Assembly */
        $actual = $form->getObject();
        $expected = (new ModelInflation())
            ->setDate(new DateTime('2001-02-01'))
            ->setId(1)
            ->setValue(1.0);
        $this->assertEquals($expected, $actual);
    }
}
