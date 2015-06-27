<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 27/06/15
 * Time: 9:58 AM
 */

namespace Althingi\Hydrator;

use PHPUnit_Framework_TestCase;
use Althingi\Form\Assembly as AssemblyForm;

class AssemblyTest extends PHPUnit_Framework_TestCase
{
    public function testHydratorExtractType()
    {
        $hydrator = new Assembly();
        $result = $hydrator->extract($this->buildDatabaseObject());

        $this->assertInternalType('array', $result);
    }

    public function testHydratorExtractKeys()
    {
        $hydrator = new Assembly();
        $result = $hydrator->extract($this->buildDatabaseObject());

        $resultKeys = array_keys($result);
        $expectedKeys = [
            'assembly_id',
            'from',
            'to'
        ];

        $this->assertCount(0, array_diff($resultKeys, $expectedKeys));
    }

    public function testForm()
    {
        $form = new AssemblyForm();
        $form->setObject($this->buildDatabaseObject());
        $form->setData(['assembly_id' => 2]);
        $isValid = $form->isValid();
        $object = $form->getObject();

        $this->assertEquals(2, $object->assembly_id);
        $this->assertTrue($isValid);
    }

    public function tesElementsInForm()
    {
        $form = new AssemblyForm();
        $elementsNames = array_keys($form->getElements());
        $expectedNames = [
            'assembly_id',
            'from',
            'to',
        ];

        $this->assertCount(0, array_diff($expectedNames, $elementsNames));

    }

    private function buildDatabaseObject()
    {
        return (object) [
            "assembly_id" => 144,
            "from" => "2014-09-09",
            "to" => null
        ];
    }
}
