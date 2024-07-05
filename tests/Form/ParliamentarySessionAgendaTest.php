<?php

namespace Althingi\Form;

use Althingi\Form\ParliamentarySessionAgenda;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ParliamentarySessionAgendaTest extends TestCase
{
    #[Test]
    public function IDisNumber()
    {
        $form = new ParliamentarySessionAgenda([
            'assembly_id' => 141,
            'kind' => 'B',
            'counter_answerer' => 'velferu00f0arru00e1u00f0herra',
            'counter_answerer_id' => 683,
            'instigator' => 'Ju00f3n Gunnarsson',
            'instigator_id' => 688,
            'issue_id' => 54,
            'issue_name' => 'stau00f0a mu00e1la u00e1 Landspu00edtalanum',
            'issue_type' => 'um',
            'issue_typename' => 'su00e9rstu00f6k umru00e6u00f0a',
            'item_id' => 2,
            'iteration_type' => '*',
            'parliamentary_session_id' => 8

        ]);
        $isValid = $form->isValid();

        $this->assertTrue($isValid);
    }

    #[Test]
    public function IDisString()
    {
        $form = new ParliamentarySessionAgenda([
            'assembly_id' => '141',
            'kind' => 'B',
            'counter_answerer' => 'velferu00f0arru00e1u00f0herra',
            'counter_answerer_id' => '683',
            'instigator' => 'Ju00f3n Gunnarsson',
            'instigator_id' => '688',
            'issue_id' => '54',
            'issue_name' => 'stau00f0a mu00e1la u00e1 Landspu00edtalanum',
            'issue_type' => 'um',
            'issue_typename' => 'su00e9rstu00f6k umru00e6u00f0a',
            'item_id' => '2',
            'iteration_type' => '*',
            'parliamentary_session_id' => '8',

            'iteration_continue' => '',
            'iteration_comment' => '',
            'comment' => '',
            'comment_type' => '',
            'posed_id' => '1',
            'posed' => '',
            'answerer_id' => '2',
            'answerer' => '',
        ]);
        $isValid = $form->isValid();

        $this->assertTrue($isValid);
    }

    #[Test]
    public function IDisEmpty()
    {
        $form = new ParliamentarySessionAgenda([
            'assembly_id' => '141',
            'kind' => 'B',
            'counter_answerer' => 'velferu00f0arru00e1u00f0herra',
            'counter_answerer_id' => '',
            'instigator' => 'Ju00f3n Gunnarsson',
            'instigator_id' => null,
            'issue_id' => '54',
            'issue_name' => 'stau00f0a mu00e1la u00e1 Landspu00edtalanum',
            'issue_type' => 'um',
            'issue_typename' => 'su00e9rstu00f6k umru00e6u00f0a',
            'item_id' => '2',
            'iteration_type' => '*',
            'parliamentary_session_id' => '8',

            'iteration_continue' => '',
            'iteration_comment' => '',
            'comment' => '',
            'comment_type' => '',
            'posed_id' => '1',
            'posed' => '',
            'answerer_id' => '',
            'answerer' => '',
        ]);
        $isValid = $form->isValid();

        $this->assertTrue($isValid);
    }
}
