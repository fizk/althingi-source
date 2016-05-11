<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Mockery;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IssueControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );
        parent::setUp();
    }

    public function testGetSuccess()
    {
        $pdo = Mockery::mock('PDO');
        $issueService = Mockery::mock('Althingi\Service\Issue')
            ->shouldReceive('get')
            ->andReturnUsing(function ($issueId, $assemblyId) {
                $this->assertEquals(100, $assemblyId);
                $this->assertEquals(200, $issueId);
                return $this->buildIssueObject();
            })
            ->once()
            ->getMock();

        $assemblyService = Mockery::mock('Althingi\Service\Assembly')
            ->shouldReceive('get')
            ->andReturn((object) ['from' => '2001-02-01', 'to' => null])
            ->once()
            ->getMock();

        $congressmanService = Mockery::mock('Althingi\Service\Congressman')
            ->shouldReceive('get')
            ->andReturn((object) ['congressman_id' => 1])
            ->once()
            ->getMock()
            ->shouldReceive('fetchAccumulatedTimeByIssue')
            ->andReturn([
                (object) [
                    'congressman_id' => 1,
                    'begin' => date('Y-m-d H:i:s')
                ]
            ])
            ->once()
            ->getMock();

        $partyService = Mockery::mock('Althingi\Service\Party')
            ->shouldReceive('getByCongressman')
            ->andReturnNull()
            ->once()
            ->getMock();

        $voteService = Mockery::mock('Althingi\Service\Vote')
            ->shouldReceive('fetchDateFrequencyByIssue')
            ->andReturn([])
            ->once()
            ->getMock();

        $speechService = Mockery::mock('Althingi\Service\Speech')
            ->shouldReceive('fetchFrequencyByIssue')
            ->andReturn([])
            ->once()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Issue', $issueService);
        $serviceManager->setService('Althingi\Service\Assembly', $assemblyService);
        $serviceManager->setService('Althingi\Service\Congressman', $congressmanService);
        $serviceManager->setService('Althingi\Service\Party', $partyService);
        $serviceManager->setService('Althingi\Service\Vote', $voteService);
        $serviceManager->setService('Althingi\Service\Speech', $speechService);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/100/thingmal/200', 'GET');

        $this->assertControllerClass('IssueController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    public function testGetResourceNotFound()
    {
        $pdo = Mockery::mock('PDO');
        $serviceMock = Mockery::mock('Althingi\Service\Issue')
            ->shouldReceive('get')
            ->andReturn(null)
            ->once()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Issue', $serviceMock);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/100/thingmal/200', 'GET');

        $this->assertControllerClass('IssueController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    public function testGetList()
    {
        $pdo = Mockery::mock('PDO');
        $serviceMock = \Mockery::mock('Althingi\Service\Issue')
            ->shouldReceive('fetchByAssembly')
            ->andReturn([(object) ['congressman_id' => 1, 'date' => date('Y-m-d H:i:s')]])
            ->never()
            ->getMock()
            ->shouldReceive('countByAssembly')
            ->andReturn(123)
            ->once()
            ->getMock();

        $partyService = Mockery::mock('Althingi\Service\Party')
            ->shouldReceive('getByCongressman')
            ->andReturnNull()
            ->getMock();

        $congressmanService = Mockery::mock('Althingi\Service\Congressman')
            ->shouldReceive('get')
            ->andReturn((object) ['congressman_id' => 1])
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Issue', $serviceMock);
        $serviceManager->setService('Althingi\Service\Party', $partyService);
        $serviceManager->setService('Althingi\Service\Congressman', $congressmanService);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/100/thingmal', 'GET');
        $this->assertControllerClass('IssueController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
        $this->assertResponseHeaderContains('Range-Unit', 'items');
        $this->assertResponseHeaderContains('Content-Range', 'items 0-25/123');
    }

    public function testGetListParams()
    {
        $pdo = Mockery::mock('PDO');
        $issueService = Mockery::mock('Althingi\Service\Issue')
            ->shouldReceive('countByAssembly')
            ->andReturn(700)
            ->getMock()
            ->shouldReceive('fetchByAssembly')
            ->andReturnUsing(function ($assemblyId, $from, $size, $order, $type) {
                $this->assertEquals(100, $assemblyId);
                $this->assertEquals(0, $from);
                $this->assertEquals(25, $size);
                $this->assertEquals('desc', $order);
                $this->assertCount(0, array_diff($type, ['l','a']));
                return [];
            })
            ->once()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdo);
        $serviceManager->setService('Althingi\Service\Issue', $issueService);

        $this->dispatch('/loggjafarthing/100/thingmal?order=desc&type=la', 'GET');
    }

    public function testPutSuccess()
    {
        $pdo = Mockery::mock('PDO');
        $serviceMock = Mockery::mock('Althingi\Service\Issue')
            ->shouldReceive('create')
            ->andReturnUsing(function ($object) {
                $this->assertEquals('n1', $object->name);
                $this->assertEquals('c1', $object->category);
                $this->assertEquals('1', $object->type);
                $this->assertEquals('tn', $object->type_name);
                $this->assertEquals('tsn', $object->type_subname);
                $this->assertEquals(100, $object->assembly_id);
                $this->assertEquals(200, $object->issue_id);
                return 10;
            })
            ->once()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Issue', $serviceMock);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/100/thingmal/200', 'PUT', [
            'name' => 'n1',
            'category' => 'c1',
            'type' => '1',
            'type_name' => 'tn',
            'type_subname' => 'tsn',
        ]);

        $this->assertControllerClass('IssueController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    public function testPutInvalidForm()
    {
        $pdo = Mockery::mock('PDO');
        $serviceMock = \Mockery::mock('Althingi\Service\Issue')
            ->shouldReceive('create')
            ->andReturnUsing(function ($object) {
                $this->assertEquals(100, $object->assembly_id);
                $this->assertEquals(200, $object->issue_id);
                $this->assertEquals('tn', $object->type_name);
                $this->assertEquals('tsn', $object->type_subname);
            })
            ->never()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Issue', $serviceMock);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/100/thingmal/200', 'PUT', [
            'type_name' => 'tn',
            'type_subname' => 'tsn',
        ]);

        $this->assertControllerClass('IssueController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    public function testPutParams()
    {
        $pdo = Mockery::mock('PDO');
        $issueService = Mockery::mock('Althingi\Service\Issue')
            ->shouldReceive('create')
            ->andReturnUsing(function ($object) {
                $this->assertEquals(100, $object->assembly_id);
                $this->assertEquals(200, $object->issue_id);
                return null;
            })
            ->once()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Issue', $issueService);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/100/thingmal/200', 'PUT', [
            'name' => 'n1',
            'category' => 'c1',
            'type' => '1',
            'type_name' => 'tn',
            'type_subname' => 'tsn',
        ]);
    }

    public function testPatch()
    {
        $pdo = Mockery::mock('PDO');
        $serviceMock = \Mockery::mock('Althingi\Service\Issue')
            ->shouldReceive('get')
                ->andReturn($this->buildIssueObject())
                ->getMock()
            ->shouldReceive('update')
            ->andReturnUsing(function ($object) {
                $this->assertObjectHasAttribute('congressman_id', $object);
                $this->assertEquals(1, $object->congressman_id);
                $this->assertEquals('n1', $object->name);
                $this->assertEquals('A', $object->category);
                return $object;
            })->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Issue', $serviceMock);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/100/thingmal/200', 'PATCH', [
            'name' => 'n1'
        ]);
        $this->assertControllerClass('IssueController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(200);
    }

    public function testPatchEntryNotFound()
    {
        $pdo = Mockery::mock('PDO');
        $serviceMock = Mockery::mock('Althingi\Service\Issue')
            ->shouldReceive('get')
                ->andReturn(null)
                ->once()
                ->getMock()
            ->shouldReceive('update')
                ->andReturn(null)
                ->never()
                ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Issue', $serviceMock);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/100/thingmal/200', 'PATCH', [
            'name' => 'n1'
        ]);

        $this->assertControllerClass('IssueController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    public function testPatchInvalidForm()
    {
        $pdo = Mockery::mock('PDO');
        $serviceMock = Mockery::mock('Althingi\Service\Issue')
            ->shouldReceive('get')
                ->andReturn($this->buildIssueObject())
                ->once()
                ->getMock()
            ->shouldReceive('update')
                ->andReturn(null)
                ->never()
                ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Issue', $serviceMock);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/100/thingmal/200', 'PATCH', [
            'name' => 'n1',
            'congressman_id' => 'I can not be a string'
        ]);
        $this->assertControllerClass('IssueController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    public function testPatchParams()
    {
        $pdo = Mockery::mock('PDO');
        $issueService = Mockery::mock('Althingi\Service\Issue')
            ->shouldReceive('get')
            ->andReturnUsing(function ($issueId, $assemblyId) {
                $this->assertEquals(200, $issueId);
                $this->assertEquals(100, $assemblyId);
                return null;
            })
            ->once()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Issue', $issueService);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/100/thingmal/200', 'PATCH');
    }

    public function testOptions()
    {
        $pdo = Mockery::mock('PDO');

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/100/thingmal/1', 'OPTIONS');

        $this->assertControllerClass('IssueController');
        $this->assertActionName('options');
        $this->assertResponseStatusCode(200);

        $expectedMethods = ['GET', 'OPTIONS', 'PUT', 'PATCH'];
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }

    public function testOptionsList()
    {
        $pdo = Mockery::mock('PDO');
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/100/thingmal', 'OPTIONS');

        $this->assertControllerClass('IssueController');
        $this->assertActionName('optionslist');
        $this->assertResponseStatusCode(200);

        $expectedMethods = ['GET', 'OPTIONS'];
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }

    public function testAssembly()
    {
        $expectedResponse = (object) [
            'bills' => [],
            'government_bills' => [],
            'types' => [],
            'votes' => [],
            'speeches' => [],
        ];

        $pdo = Mockery::mock('PDO');
        $issueService = Mockery::mock('Althingi\Service\Issue')
            ->shouldReceive('fetchBillStatisticsByAssembly')
            ->andReturnUsing(function ($assemblyId) {
                $this->assertEquals(100, $assemblyId);
                return [];
            })
            ->once()
            ->getMock()
            ->shouldReceive('fetchGovernmentBillStatisticsByAssembly')
            ->andReturn([])
            ->getMock()
            ->shouldReceive('fetchStateByAssembly')
            ->andReturn([])
            ->getMock();
        $voteService = Mockery::mock('Althingi\Service\Vote')
            ->shouldReceive('fetchFrequencyByAssembly')
            ->andReturn([])
            ->once()
            ->getMock();
        $speechService = Mockery::mock('Althingi\Service\Speech')
            ->shouldReceive('fetchFrequencyByAssembly')
            ->andReturn([])
            ->once()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Issue', $issueService);
        $serviceManager->setService('Althingi\Service\Vote', $voteService);
        $serviceManager->setService('Althingi\Service\Speech', $speechService);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/100/samantekt', 'GET');

        $this->assertControllerClass('IssueController');
        $this->assertActionName('assembly');

        $this->assertEquals($expectedResponse, json_decode($this->getResponse()->getContent()));
    }

    private function buildIssueObject()
    {
        return (object) [
            "issue_id"  => 2,
            "assembly_id"  => 144,
            "category"  => "A",
            "name"  => "Virðisaukaskattur o.fl.",
            "type"  => 'l',
            "type_name"  => "Frumvarp til laga",
            "type_subname"  => "lagafrumvarp",
            "status" => "Samþykkt sem lög frá Alþingi.",
            "congressman_id" => 1,
            "date" => '2001-02-02'
        ];
    }
}
