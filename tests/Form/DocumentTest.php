<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 24/03/2016
 * Time: 4:36 PM
 */

namespace Althingi\Form;

use PHPUnit_Framework_TestCase;

class DocumentTest extends PHPUnit_Framework_TestCase
{
    public function testObjectGoesThrough()
    {
        $inputData = [
            'issue_id' => 1,
            'assembly_id' => 1,
            'document_id' => 1,
            'date' => '2001-01-01',
            'url' => '/some/url',
            'type' => 'some=type',
        ];

        $form = new Document();
        $form->setData($inputData)->isValid();
        $object = $form->getObject();

        $this->assertEquals((object) $inputData, $object);
    }

}
