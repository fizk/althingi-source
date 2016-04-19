<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 24/03/2016
 * Time: 4:36 PM
 */

namespace Althingi\Form;

use PHPUnit_Framework_TestCase;

class SessionTest extends PHPUnit_Framework_TestCase
{
    public function testEmptyToValue()
    {
        $form = new Session();
        $form->setData([
            'session_id' => 1,
            'congressman_id' => 1,
            'constituency_id' => 1,
            'assembly_id' => 1,
            'from' => '2001-01-01',
            'to' => '',
            'type' => 'some',
            'party_id' => 1,

        ])->isValid();

        $this->assertNull($form->getObject()->to);
    }

    public function testNullToValue()
    {
        $form = new Session();
        $form->setData([
            'session_id' => 1,
            'congressman_id' => 1,
            'constituency_id' => 1,
            'assembly_id' => 1,
            'from' => '2001-01-01',
            'to' => null,
            'type' => 'some',
            'party_id' => 1,

        ])->isValid();

        $this->assertNull($form->getObject()->to);
    }

    public function testNonEmptyToValue()
    {
        $form = new Session();
        $form->setData([
            'session_id' => 1,
            'congressman_id' => 1,
            'constituency_id' => 1,
            'assembly_id' => 1,
            'from' => '2001-01-01',
            'to' => '2001-01-01',
            'type' => 'some',
            'party_id' => 1,

        ])->isValid();

        $this->assertNotNull($form->getObject()->to);
    }
}
