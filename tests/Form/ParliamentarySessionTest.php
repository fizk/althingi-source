<?php

namespace Althingi\Form;

use Althingi\Form\ParliamentarySession;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ParliamentarySessionTest extends TestCase
{
    #[Test]
    public function emptyValuesAreAllowed()
    {
        $form = new ParliamentarySession([
            'parliamentary_session_id' => '1',
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
        $form = new ParliamentarySession([
            'parliamentary_session_id' => '-1',
            'assembly_id' => '2',
            'name' => '',
            'from' => '',
            'to' => '',
        ]);
        $isValid = $form->isValid();

        $this->assertTrue($isValid);
    }
}
