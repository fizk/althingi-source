<?php

namespace Althingi\Form;

use Althingi\Model\KindEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VoteTest extends TestCase
{
    public static function providerFunction()
    {
        return [
            [["yes" => 48, "no" => 0, "inaction" => "0",], 48, 0 , 0],
            [["yes" => '48', "no" => '0', "inaction" => "0",], 48, 0 , 0],
            [["yes" => 'notAnumber', "no" => '0', "inaction" => "0",], 0, 0 , 0],
            [["yes" => 'true', "no" => '0', "inaction" => "0",], 0, 0 , 0],
            [["yes" => 'false', "no" => 'false', "inaction" => "false",], 0, 0 , 0],
            [["yes" => '0', "no" => 0, "inaction" => "0",], 0, 0 , 0],
            [["yes" => '', "no" => 0, "inaction" => "0",], 0, 0 , 0],
            [["yes" => 'e', "no" => 0, "inaction" => "0",], 0, 0 , 0],
            [["yes" => 0, "no" => 10, "inaction" => "null",], 0, 10 , 0],
        ];
    }

    #[DataProvider('providerFunction')]
    public function testTrue($input, $yes, $no, $inaction)
    {
        $form = new Vote([
           "assembly_id" => 146,
           "committee_to" => null,
           "date" => "2016-12-07 13:32:07",
           "document_id" => 1,
           "inaction" => $input['inaction'],
           "issue_id" => 1,
           "method" => "atkvæðagreiðslukerfi",
           "no" => $input['no'],
           "outcome" => "samþykkt",
           "type" => "Of skammt var liðið frá útbýtingu --- Afbrigði",
           "vote_id" => 53954,
           "yes" => $input['yes'],
           'kind' => KindEnum::A->value
        ]);

        $form->isValid();
        $model = $form->getModel();

        $this->assertEquals($yes, $model->getYes());
        $this->assertEquals($no, $model->getNo());
        $this->assertEquals($inaction, $model->getInaction());
    }
}
