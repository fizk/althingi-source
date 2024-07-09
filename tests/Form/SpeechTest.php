<?php

namespace Althingi\Form;

use Althingi\Form\Speech;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SpeechTest extends TestCase
{
    #[Test]
    public function validForm()
    {
        $form = (new Speech([
            "assembly_id" => "144",
            "congressman_id" => "652",
            "congressman_type" => "fjármála- og efnahagsráðherra",
            "from" => "2014-09-11 10:36:52",
            "id" => "20140911T103652",
            "issue_id" => "1",
            "iteration" => "1",
            "kind" => "a",
            "parliamentary_session_id" => "3",
            "speech_id" => "20140911T103652",
            "text" => "<ræðutexti xmlns=\"http://skema.althingi.is/skema\">\n</ræðutexti>",
            "to" => "2014-09-11 11:07:24",
            "type" => "flutningsræða",
            "validated" => "true"

        ]));
        $form->isValid();

        $this->assertTrue($form->isValid());
    }

}
