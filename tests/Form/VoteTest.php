<?php
/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 5/18/17
 * Time: 8:56 AM
 */

namespace Althingi\Form;

use PHPUnit_Framework_TestCase;

class VoteTest extends PHPUnit_Framework_TestCase
{
    public function providerFunction()
    {
        return [
            [["yes" => 48, "no" => 0, "inaction" => "0",], 48, 0 , 0],
            [["yes" => '0', "no" => 0, "inaction" => "0",], 0, 0 , 0],
            [["yes" => '', "no" => 0, "inaction" => "0",], 0, 0 , 0],
            [["yes" => 'e', "no" => 0, "inaction" => "0",], 0, 0 , 0],
            [["yes" => 0, "no" => 10, "inaction" => "null",], 0, 10 , 0],
        ];
    }

    /**
     * @dataProvider providerFunction
     */
    public function testTrue($input, $yes, $no, $inaction)
    {
        $inputData = array_merge([
            "assembly_id" => 146,
            "committee_to" => null,
            "date" => "2016-12-07 13:32:07",
            "document_id" => 1,
            "inaction" => "0",
            "issue_id" => 1,
            "method" => "atkvæðagreiðslukerfi",
            "no" => 0,
            "outcome" => "samþykkt",
            "type" => "Of skammt var liðið frá útbýtingu --- Afbrigði",
            "vote_id" => 53954,
            "yes" => 48,
        ], $input);

        $form = (new \Althingi\Form\Vote())
                    ->setData($inputData);
        $form->isValid();

        $outputData = $form->getObject();

        $this->assertNotNull($outputData->getYes());
        $this->assertNotNull($outputData->getNo());
        $this->assertNotNull($outputData->getInaction());

        $this->assertTrue($yes === $outputData->getYes());
        $this->assertTrue($no === $outputData->getNo());
        $this->assertTrue($inaction === $outputData->getInaction());
    }
}
