<?php
namespace AlthingiTest\StatusNormalizer;

use PHPUnit\Framework\TestCase;
use \Althingi\Hydrator\Status as StatusHydrator;
use \Althingi\Model\Status as StatusModel;

class StatusNormalizerLTest extends TestCase
{
    public function testTrue()
    {
//        $data = [
//            [
//                'assembly_id' => 146,
//                'issue_id' => 258,
//                'committee_id' => null,
//                'speech_id' => null,
//                'document_id' => 356,
//                'committee_name' => null,
//                'date' => "2017-03-09 15:41:00",
//                'title' => "frumvarp",
//                'type' => 'document',
//                'completed' => 1
//            ],[
//                'assembly_id' => 146,
//                'issue_id' => 258,
//                'committee_id' => null,
//                'speech_id' => '20170322T183852',
//                'document_id' => null,
//                'committee_name' => null,
//                'date' => "2017-03-22 18:38:52",
//                'title' => "1. umræða",
//                'type' => 'speech',
//                'completed' => 1
//            ],[
//                'assembly_id' => 146,
//                'issue_id' => 258,
//                'committee_id' => 206,
//                'speech_id' => null,
//                'document_id' => null,
//                'committee_name' => "stjórnskipunar- og eftirlitsnefnd",
//                'date' => "2017-03-28 09:00:00",
//                'title' => "í nefnd",
//                'type' => 'committee',
//                'completed' => 1
//            ],[
//                'assembly_id' => 146,
//                'issue_id' => 258,
//                'committee_id' => 206 ,
//                'speech_id' => null,
//                'document_id' => null,
//                'committee_name' => "stjórnskipunar- og eftirlitsnefnd",
//                'date' => "2017-05-18 09:10:00",
//                'title' => "í nefnd",
//                'type' => 'committee',
//                'completed' => 1
//            ],[
//                'assembly_id' => 146,
//                'issue_id' => 258,
//                'committee_id' => 206 ,
//                'speech_id' => null,
//                'document_id' => null,
//                'committee_name' => "stjórnskipunar- og eftirlitsnefnd",
//                'date' => "2017-05-23 09:00:00",
//                'title' => "í nefnd",
//                'type' => 'committee',
//                'completed' => 1
//            ],[
//                'assembly_id' => 146,
//                'issue_id' => 258,
//                'committee_id' => 206,
//                'speech_id' => null,
//                'document_id' => null,
//                'committee_name' => "stjórnskipunar- og eftirlitsnefnd",
//                'date' => "2017-05-26 13:00:00",
//                'title' => "í nefnd",
//                'type' => 'committee',
//                'completed' => 1
//            ],[
//                'assembly_id' => 146,
//                'issue_id' => 258,
//                'committee_id' => 206,
//                'speech_id' => null,
//                'document_id' => null,
//                'committee_name' => "stjórnskipunar- og eftirlitsnefnd",
//                'date' => "2017-05-29 10:10:00",
//                'title' => "í nefnd",
//                'type' => 'committee',
//                'completed' => 1
//            ],[
//                'assembly_id' => 146,
//                'issue_id' => 258,
//                'committee_id' => null,
//                'speech_id' => null,
//                'document_id' => 941,
//                'committee_name' => null,
//                'date' => "2017-05-29 20:16:00",
//                'title' => "nál. með frávt.",
//                'type' => 'document',
//                'completed' => 1
//            ],[
//                'assembly_id' => 146,
//                'issue_id' => 258,
//                'committee_id' => null,
//                'speech_id' => '20170530T190020',
//                'document_id' => null,
//                'committee_name' => null,
//                'date' => "2017-05-30 19:00:20",
//                'title' => "2. umræða",
//                'type' => 'speech',
//                'completed' => 1
//            ],
//        ];
//
//        $models = array_map(function ($item) {
//            return (new StatusHydrator())->hydrate($item, new StatusModel());
//        }, $data);
//
//        $normalizer = new StatusNormalizerL();
        $this->assertTrue(true);
    }
}
