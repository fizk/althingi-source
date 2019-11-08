<?php

namespace AlthingiTest\Store;

use AlthingiTest\StorageConnection;
use Althingi\Store;
use Althingi\Model;
use PHPUnit\Framework\TestCase;
use Zumba\PHPUnit\Extensions\Mongo\DataSet\DataSet;

class IssueTest extends TestCase
{
    use StorageConnection;

    public function testGet()
    {
        $store = (new Store\Issue())->setStore($this->database);
        $issue = $store->get(149, 1, 'A');
        $this->assertInstanceOf(Model\IssueProperties::class, $issue);
    }

    public function testGetNotFound()
    {
        $store = (new Store\Issue())->setStore($this->database);
        $issue = $store->get(1, 1, 'A');
        $this->assertNull($issue);
    }

    public function testFetchByAssembly()
    {
        $store = (new Store\Issue())->setStore($this->database);
        $issues = $store->fetchByAssembly(149);
        $this->assertCount(2, $issues);
    }

    public function testFetchAByAssembly()
    {
        $store = (new Store\Issue())->setStore($this->database);
        $issues = $store->fetchByAssembly(149, 0, 10, [], [], ['A']);
        $this->assertCount(1, $issues);
    }

    public function testCountByAssembly()
    {
        $store = (new Store\Issue())->setStore($this->database);
        $issues = $store->countByAssembly(149, [], [], ['A']);
        $this->assertEquals(1, $issues);
    }

    /**
     * Retrieve a DataSet object.
     *
     * @return \Zumba\PHPUnit\Extensions\Mongo\DataSet\DataSet
     */
    protected function getMongoDataSet()
    {
        $dataSet = new DataSet($this->getMongoConnection());
        $dataSet->setFixture([
            'issue' => [
                [

                    "assembly" => [
                        "assembly_id" => 149
                    ],
                    "issue" => [
                        "issue_id" => 1,
                        "assembly_id" => 149,
                        "congressman_id" => null,
                        "category" => "A",
                        "name" => "fjárlög 2019",
                        "sub_name" => null,
                        "type" => "l",
                        "type_name" => "Frumvarp til laga",
                        "type_subname" => "lagafrumvarp",
                        "status" => "Samþykkt sem lög frá Alþingi",
                        "question" => null,
                        "goal" => "selja fasteignir.",
                        "major_changes" => "Lögð eru til aukin framlög til ",
                        "changes_in_law" => "Gera þarf ",
                        "costs_and_revenues" => "Áætlað er að tekjur fyrir ",
                        "deliveries" => "Frumvarpið varð að lögum með þeim ",
                        "additional_information" => ""
                    ],
                    "categories" => [
                        [
                            "category_id" => 6,
                            "super_category_id" => 2,
                            "title" => "Fjárreiður ríkisins",
                            "description" => "þ.m.t. fjárlög, lánamál "
                        ]
                    ],
                    "date" => null,
                    "government_issue" => true,
                    "proponents" => [
                        [
                            "congressman" => [
                                "congressman_id" => 652,
                                "name" => "Bjarni Benediktsson",
                                "birth" => "1970-01-26",
                                "death" => null,
                                "abbreviation" => "BjarnB",
                                "party" => [
                                    "party_id" => 35,
                                    "name" => "Sjálfstæðisflokkur",
                                    "abbr_short" => "S",
                                    "abbr_long" => "Sjálfstfl.",
                                    "color" => "00adef"
                                ],
                                "assembly" => null,
                                "constituency" => [
                                    "constituency_id" => 52,
                                    "name" => "Suðvesturkjördæmi",
                                    "abbr_short" => "SV",
                                    "abbr_long" => "Suðvest.",
                                    "description" => "Til þess teljast efti",
                                    "date" => "2018-09-11"
                                ]
                            ],
                            "order" => 1,
                            "minister" => "fjármála- og efnahagsráðherra"
                        ]
                    ],
                    "speakers" => [],
                    "speech_count" => 987,
                    "speech_time" => 188452,
                    "super_categories" => [
                        [
                            "super_category_id" => 2,
                            "title" => "Hagstjórn"
                        ]
                    ],
                    "document_type" => "stjórnarfrumvarp",
                    "document_url" => "http=>//www.althingi.is/altext/149/s/0001.html"
                ],
                [

                    "assembly" => [
                        "assembly_id" => 149
                    ],
                    "issue" => [
                        "issue_id" => 20,
                        "assembly_id" => 149,
                        "congressman_id" => null,
                        "category" => "B",
                        "name" => "nýting fjármuna heilbrigðiskerfisins",
                        "sub_name" => null,
                        "type" => "ft",
                        "type_name" => "óundirbúinn fyrirspurnatími",
                        "type_subname" => null,
                        "status" => null,
                        "question" => null,
                        "goal" => null,
                        "major_changes" => null,
                        "changes_in_law" => null,
                        "costs_and_revenues" => null,
                        "deliveries" => null,
                        "additional_information" => null
                    ],
                    "categories" => [ ],
                    "date" => null,
                    "government_issue" => false,
                    "proponents" => [ ],
                    "speakers" => [
                        [
                            "congressman" => [
                                "congressman_id" => 729,
                                "name" => "Sigmundur Davíð Gunnlaugsson",
                                "birth" => "1975-03-12",
                                "death" => null,
                                "abbreviation" => "SDG",
                                "party" => [
                                    "party_id" => 47,
                                    "name" => "Miðflokkurinn",
                                    "abbr_short" => "M",
                                    "abbr_long" => "Miðfl.",
                                    "color" => "199094"
                                ],
                                "assembly" => null,
                                "constituency" => [
                                    "constituency_id" => 49,
                                    "name" => "Norðausturkjördæmi",
                                    "abbr_short" => "NA",
                                    "abbr_long" => "Norðaust.",
                                    "description" => "Til þess teljast ",
                                    "date" => "2018-09-11"
                                ]
                            ],
                            "time" => 194
                        ],
                        [
                            "congressman" => [
                                "congressman_id" => 652,
                                "name" => "Bjarni Benediktsson",
                                "birth" => "1970-01-26",
                                "death" => null,
                                "abbreviation" => "BjarnB",
                                "party" => [
                                    "party_id" => 35,
                                    "name" => "Sjálfstæðisflokkur",
                                    "abbr_short" => "S",
                                    "abbr_long" => "Sjálfstfl.",
                                    "color" => "00adef"
                                ],
                                "assembly" => null,
                                "constituency" => [
                                    "constituency_id" => 52,
                                    "name" => "Suðvesturkjördæmi",
                                    "abbr_short" => "SV",
                                    "abbr_long" => "Suðvest.",
                                    "description" => "Til þess ",
                                    "date" => "2018-09-11"
                                ]
                            ],
                            "time" => 216
                        ]
                    ],
                    "speech_count" => 4,
                    "speech_time" => 410,
                    "super_categories" => [ ]
                ],
                [

                    "assembly" => [
                        "assembly_id" => 150
                    ],
                    "issue" => [
                        "issue_id" => 20,
                        "assembly_id" => 150,
                        "congressman_id" => null,
                        "category" => "B",
                        "name" => "nýting fjármuna heilbrigðiskerfisins",
                        "sub_name" => null,
                        "type" => "ft",
                        "type_name" => "óundirbúinn fyrirspurnatími",
                        "type_subname" => null,
                        "status" => null,
                        "question" => null,
                        "goal" => null,
                        "major_changes" => null,
                        "changes_in_law" => null,
                        "costs_and_revenues" => null,
                        "deliveries" => null,
                        "additional_information" => null
                    ],
                    "categories" => [ ],
                    "date" => null,
                    "government_issue" => false,
                    "proponents" => [ ],
                    "speakers" => [
                        [
                            "congressman" => [
                                "congressman_id" => 729,
                                "name" => "Sigmundur Davíð Gunnlaugsson",
                                "birth" => "1975-03-12",
                                "death" => null,
                                "abbreviation" => "SDG",
                                "party" => [
                                    "party_id" => 47,
                                    "name" => "Miðflokkurinn",
                                    "abbr_short" => "M",
                                    "abbr_long" => "Miðfl.",
                                    "color" => "199094"
                                ],
                                "assembly" => null,
                                "constituency" => [
                                    "constituency_id" => 49,
                                    "name" => "Norðausturkjördæmi",
                                    "abbr_short" => "NA",
                                    "abbr_long" => "Norðaust.",
                                    "description" => "Til þess teljast ",
                                    "date" => "2018-09-11"
                                ]
                            ],
                            "time" => 194
                        ],
                        [
                            "congressman" => [
                                "congressman_id" => 652,
                                "name" => "Bjarni Benediktsson",
                                "birth" => "1970-01-26",
                                "death" => null,
                                "abbreviation" => "BjarnB",
                                "party" => [
                                    "party_id" => 35,
                                    "name" => "Sjálfstæðisflokkur",
                                    "abbr_short" => "S",
                                    "abbr_long" => "Sjálfstfl.",
                                    "color" => "00adef"
                                ],
                                "assembly" => null,
                                "constituency" => [
                                    "constituency_id" => 52,
                                    "name" => "Suðvesturkjördæmi",
                                    "abbr_short" => "SV",
                                    "abbr_long" => "Suðvest.",
                                    "description" => "Til þess ",
                                    "date" => "2018-09-11"
                                ]
                            ],
                            "time" => 216
                        ]
                    ],
                    "speech_count" => 4,
                    "speech_time" => 410,
                    "super_categories" => [ ]
                ]
            ]
        ]);

        return $dataSet;
    }
}
