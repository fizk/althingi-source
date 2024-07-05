<?php

namespace Althingi\Form;

use Althingi\Form\Plenary;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PlenaryTest extends TestCase
{
    #[Test]
    public function emptyValuesAreAllowed()
    {
        $form = new Plenary([
            'plenary_id' => '1',
            'assembly_id' => '2',
            'name' => '',
            'from' => '',
            'to' => '',

        ]);
        $isValid = $form->isValid();

        $this->assertTrue($isValid);
    }

    #[Test]
    public function IDisNegativeNumber()
    {
        $form = new Plenary([
            'plenary_id' => '-1',
            'assembly_id' => '2',
            'name' => '',
            'from' => '',
            'to' => '',
        ]);
        $isValid = $form->isValid();

        $this->assertTrue($isValid);
    }
}
