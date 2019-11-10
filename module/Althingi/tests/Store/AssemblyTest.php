<?php

namespace AlthingiTest\Store;

use AlthingiTest\StorageConnection;
use Althingi\Store;
use Althingi\Model;
use PHPUnit\Framework\TestCase;
use Zumba\PHPUnit\Extensions\Mongo\DataSet\DataSet;
use DateTime;

class AssemblyTest extends TestCase
{
    use StorageConnection;

    public function testGet()
    {
        $store = (new Store\Assembly())->setStore($this->database);
        $assembly = $store->get(1);

        $expected = (new Model\AssemblyProperties())
            ->setAssembly(
                (new Model\Assembly())
                    ->setAssemblyId(1)
                    ->setFrom(new DateTime('2001-01-01'))
                    ->setTo(new DateTime('2001-01-01'))
            );


        $this->assertEquals($expected, $assembly);
        $this->assertInstanceOf(Model\AssemblyProperties::class, $assembly);
    }

    public function testFetch()
    {
        $store = (new Store\Assembly())->setStore($this->database);
        $assembly = $store->fetch();

        $expected = [
            (new Model\AssemblyProperties())
                ->setAssembly(
                    (new Model\Assembly())
                    ->setAssemblyId(2)
                    ->setFrom(new DateTime('2001-01-01'))
                    ->setTo(null)
                ),
            (new Model\AssemblyProperties())
                ->setAssembly(
                    (new Model\Assembly())
                    ->setAssemblyId(1)
                    ->setFrom(new DateTime('2001-01-01'))
                    ->setTo(new DateTime('2001-01-01'))
                ),
            ];


        $this->assertEquals($expected, $assembly);
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
            'assembly' => [
                [
                    'assembly' => [
                        'assembly_id' => 1,
                        'from' => new \MongoDB\BSON\UTCDateTime(strtotime('2001-01-01') * 1000),
                        'to' => new \MongoDB\BSON\UTCDateTime(strtotime('2001-01-01') * 1000),
                    ]
                ],
                [
                    'assembly' => [
                        'assembly_id' => 2,
                        'from' => new \MongoDB\BSON\UTCDateTime(strtotime('2001-01-01') * 1000),
                        'to' => null,
                    ]
                ],
            ]
        ]);

        return $dataSet;
    }
}
