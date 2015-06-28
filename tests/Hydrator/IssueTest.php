<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 27/06/15
 * Time: 8:40 AM
 */

namespace Althingi\Hydrator;

use Althingi\Form\Issue as IssueForm;
use PHPUnit_Framework_TestCase;

class IssueTest extends PHPUnit_Framework_TestCase
{
    public function testExtractReturnType()
    {
        $hydrator = new Issue();
        $result = $hydrator->extract($this->buildDatabaseObject());

        $this->assertInternalType('array', $result);
    }

    public function testExtractReturnKeys()
    {
        $hydrator = new Issue();
        $result = $hydrator->extract($this->buildDatabaseObject());

        $returnedKeys = array_keys($result);
        $expectedKeys = [
            'issue_id',
            'assembly_id',
            'congressman_id',
            'category',
            'name',
            'type',
            'type_name',
            'type_subname',
            'status'
        ];

        $this->assertCount(0, array_diff($returnedKeys, $expectedKeys));
    }

    public function testHydrateReturnType()
    {
        $hydrator = new Issue();
        $object = $hydrator->hydrate([], (object)[]);
        $this->assertInternalType('object', $object);
    }

    public function testHydrateReturnProperties()
    {
        $hydrator = new Issue();
        $object = $hydrator->hydrate([], (object)[]);
        $this->assertInternalType('object', $object);
    }

    public function testFullObjectWithForm()
    {
        $inputDate = ['hundur' => 'voff voff'];

        $form = new IssueForm();
        $form->setObject($this->buildDatabaseObject());
        $form->setData($inputDate);
        $validForm= $form->isValid();
        $object = $form->getObject();

        $this->assertTrue($validForm);
        $this->assertObjectNotHasAttribute('hundur', $object);
        $this->assertObjectHasAttribute('congressman_id', $object);
    }

    public function testEmptyObjectWithForm()
    {
        $inputData = array_merge(
            ['hundur' => 'woff'],
            $this->buildPostDataArray()
        );

        $form = new IssueForm();
        $form->setData($inputData);
        $validForm = $form->isValid();
        $object = $form->getObject();

        $this->assertTrue($validForm);
        $this->assertObjectNotHasAttribute('hundur', $object);
        $this->assertObjectHasAttribute('congressman_id', $object);
    }

    public function testFormElementReturn()
    {
        $form = new IssueForm();
        $formElementsNames = array_keys($form->getElements());
        $expectedElementsNames = array_keys($this->buildPostDataArray());

        $this->assertCount(0, array_diff($formElementsNames, $expectedElementsNames));
    }

    /**
     * @return object
     */
    private function buildDatabaseObject()
    {
        return (object) [
            "issue_id" => 5,
            "assembly_id" => 144,
            "category" => "A",
            "name" => "Hafnalög",
            "type" => 1,
            "type_name" => "Frumvarp til laga",
            "type_subname" => "lagafrumvarp",
            "status" => "Samþykkt sem lög frá Alþingi.",
            "foreman" => (object)[
                "congressman_id" => "1163",
                "name" => "Hanna Birna Kristjánsdóttir",
                "birth" => "1966-10-12",
                "death" => null
            ],
            "time" => "00:40:54"
        ];
    }

    /**
     * @return array
     */
    private function buildPostDataArray()
    {
        return [
            'issue_id' => 1,
            'assembly_id' => 1,
            'congressman_id' => 3,
            'name' => 'name',
            'category' => 'category',
            'type'  => 'type',
            'type_name' => 'typename',
            'type_subname' => 'typesubname',
            'status' => null
        ];
    }
}
