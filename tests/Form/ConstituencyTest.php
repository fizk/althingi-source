<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 24/03/2016
 * Time: 4:36 PM
 */

namespace Althingi\Form;

use PHPUnit_Framework_TestCase;

class ConstituencyTest extends PHPUnit_Framework_TestCase
{
    public function testObjectRunsThrough()
    {
        $inputData = [
            'constituency_id' => 1,
            'name' => 'some_short-name',
            'abbr_short' => 'some_abbr-short',
            'abbr_long' => 'some_abbr-long',
            'description' => 'some-description'
        ];

        $form = new Constituency();
        $form->setData($inputData)->isValid();

        $object = $form->getObject();

        $this->assertObjectHasAttribute('constituency_id', $object);
        $this->assertObjectHasAttribute('name', $object);
        $this->assertObjectHasAttribute('abbr_short', $object);
        $this->assertObjectHasAttribute('abbr_long', $object);
        $this->assertObjectHasAttribute('description', $object);

        $this->assertEquals((object) $inputData, $object);
    }
}
