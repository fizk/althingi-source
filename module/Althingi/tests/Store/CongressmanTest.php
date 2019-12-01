<?php

namespace AlthingiTest\Store;

use AlthingiTest\StorageConnection;
use Althingi\Store;
use Althingi\Model;
use PHPUnit\Framework\TestCase;
use Zumba\PHPUnit\Extensions\Mongo\DataSet\DataSet;
use DateTime;

class CongressmanTest extends TestCase
{
    use StorageConnection;

    public function testGetByAssembly()
    {
        $store = (new Store\Congressman())->setStore($this->database);
        $congressman = $store->getByAssembly(1, 1);

        $this->assertInstanceOf(Model\CongressmanPartyProperties::class, $congressman);
    }

    public function testGetByAssemblyNotFound()
    {
        $store = (new Store\Congressman())->setStore($this->database);
        $congressman = $store->getByAssembly(100, 100);

        $this->assertNull($congressman);
    }

    public function testFetchTimeByAssembly()
    {
        $store = (new Store\Congressman())->setStore($this->database);
        $categories = $store->fetchTimeByAssembly(1);

        $party = (new Model\Party())
            ->setPartyId(35)
            ->setAbbrLong('Sjálfstfl.')
            ->setAbbrShort('S')
            ->setColor('00adef')
            ->setName('Sjálfstæðisflokkur');
        $constituency = (new Model\Constituency())
            ->setConstituencyId(44)
            ->setName('Reykjavíkurkjördæmi norður')
            ->setAbbrShort('RN')
            ->setAbbrLong('Reykv. n.')
            ->setDescription('Reykjavík');

        $expected = [
            (new Model\CongressmanPartyProperties())
                ->setCongressman(
                    (new Model\CongressmanValue())
                        ->setCongressmanId(2)
                        ->setName('name 2')
                        ->setAbbreviation('abbr')
                        ->setBirth(new DateTime('2001-01-01'))
                        ->setValue(20)
                )
                ->setParty($party)
                ->setConstituency($constituency)
                ->setAssembly((new Model\Assembly())->setAssemblyId(1)),
            (new Model\CongressmanPartyProperties())
                ->setCongressman(
                    (new Model\CongressmanValue())
                        ->setCongressmanId(1)
                        ->setName('name 1')
                        ->setAbbreviation('abbr')
                        ->setBirth(new DateTime('2001-01-01'))
                        ->setValue(10)
                )
                ->setParty($party)
                ->setConstituency($constituency)
                ->setAssembly((new Model\Assembly())->setAssemblyId(1)),
        ];

        $this->assertEquals($expected, $categories);
    }

    public function testGetAverageAgeByAssembly()
    {
        $store = (new Store\Congressman())->setStore($this->database);
        $categories = $store->getAverageAgeByAssembly(1, new DateTime('2002-01-01'));

        $expected = 1; //average age is one-year-old

        $this->assertEquals($expected, $categories);
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
            'congressman' => [
                [
                    "assembly" => [
                        "assembly_id" => 1,
                    ],
                    "speech_time" => 10,
                    "congressman" => [
                        "congressman_id" => 1,
                        "name" => "name 1",
                        "birth" => "2001-01-01",
                        "death" => null,
                        "abbreviation" => "abbr",
                        "party" => [
                            "party_id" => 35,
                            "name" => "Sjálfstæðisflokkur",
                            "abbr_short" => "S",
                            "abbr_long" => "Sjálfstfl.",
                            "color" => "00adef"
                        ],
                        "assembly" => null,
                        "constituency" => [
                            "constituency_id" => 44,
                            "name" => "Reykjavíkurkjördæmi norður",
                            "abbr_short" => "RN",
                            "abbr_long" => "Reykv. n.",
                            "description" => "Reykjavík",
                            "date" => "2018-09-11"
                        ]
                    ],
                    "parties" => [],
                ],
                [
                    "assembly" => [
                        "assembly_id" => 1,
                    ],
                    "speech_time" => 20,
                    "congressman" => [
                        "congressman_id" => 2,
                        "name" => "name 2",
                        "birth" => "2001-01-01",
                        "death" => null,
                        "abbreviation" => "abbr",
                        "party" => [
                            "party_id" => 35,
                            "name" => "Sjálfstæðisflokkur",
                            "abbr_short" => "S",
                            "abbr_long" => "Sjálfstfl.",
                            "color" => "00adef"
                        ],
                        "assembly" => null,
                        "constituency" => [
                            "constituency_id" => 44,
                            "name" => "Reykjavíkurkjördæmi norður",
                            "abbr_short" => "RN",
                            "abbr_long" => "Reykv. n.",
                            "description" => "Reykjavík",
                            "date" => "2018-09-11"
                        ]
                    ],
                    "parties" => [],
                ],
                [
                    "assembly" => [
                        "assembly_id" => 2,
                    ],
                    "speech_time" => 20,
                    "congressman" => [
                        "congressman_id" => 3,
                        "name" => "name 3",
                        "birth" => "2001-01-01",
                        "death" => null,
                        "abbreviation" => "abbr",
                        "party" => [
                            "party_id" => 35,
                            "name" => "Sjálfstæðisflokkur",
                            "abbr_short" => "S",
                            "abbr_long" => "Sjálfstfl.",
                            "color" => "00adef"
                        ],
                        "assembly" => null,
                        "constituency" => [
                            "constituency_id" => 44,
                            "name" => "Reykjavíkurkjördæmi norður",
                            "abbr_short" => "RN",
                            "abbr_long" => "Reykv. n.",
                            "description" => "Reykjavík",
                            "date" => "2018-09-11"
                        ]
                    ],
                    "parties" => [],
                ]
            ]
        ]);

        return $dataSet;
    }
}
