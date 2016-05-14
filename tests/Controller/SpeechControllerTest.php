<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Mockery;

class SpeechControllerTest extends AbstractHttpControllerTestCase
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
        $speechServiceMock = Mockery::mock('Althingi\Service\Speech')
            ->shouldReceive('countByIssue')
                ->andReturn(100)
                ->getMock()
            ->shouldReceive('fetch')
                ->andReturnUsing(function ($speechId, $assemblyId, $issueId) {
                    $this->assertEquals(4, $speechId);
                    $this->assertEquals(1, $assemblyId);
                    $this->assertEquals(3, $issueId);

                    return array_map(function ($id) {
                        return (object)[
                            'speech_id' => $id,
                            'position' => $id,
                            'from' => '2000-01-01 00:00:00',
                            'text' => '<?xml version="1.0" ?><root />',
                            'congressman_id' => 1,
                        ];
                    }, range(1, 10));
                })
                ->getMock();
        $congressmanServiceMock = Mockery::mock('Althingi\Service\Congressman')
            ->shouldReceive('get')
            ->andReturn((object)[])
            ->getMock();
        $partyServiceMock = Mockery::mock('Althingi\Service\Party')
            ->shouldReceive('getByCongressman')
            ->andReturn((object)[])
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Speech', $speechServiceMock);
        $serviceManager->setService('Althingi\Service\Congressman', $congressmanServiceMock);
        $serviceManager->setService('Althingi\Service\Party', $partyServiceMock);


        $this->dispatch('/loggjafarthing/1/thingmal/3/raedur/4', 'GET');

        $this->assertControllerClass('SpeechController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(206);
    }

    public function testGetRangeHeaders()
    {
        $speechServiceMock = Mockery::mock('Althingi\Service\Speech')
            ->shouldReceive('countByIssue')
            ->andReturn(100)
            ->getMock()
            ->shouldReceive('fetch')
            ->andReturn(array_map(function ($i) {
                return (object) [
                    'congressman_id' => 1,
                    'text' => '',
                    'from' => '2000-01-01 00:00:00',
                    'position' => $i
                ];
            }, range(25, 49)))
            ->getMock();

        $congressmanServiceMock = Mockery::mock('Althingi\Service\Congressman')
            ->shouldReceive('get')
            ->andReturn((object) [])
            ->getMock();

        $partyServiceMock = Mockery::mock('Althingi\Service\Party')
            ->shouldReceive('getByCongressman')
            ->andReturn((object) [])
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Speech', $speechServiceMock);
        $serviceManager->setService('Althingi\Service\Congressman', $congressmanServiceMock);
        $serviceManager->setService('Althingi\Service\Party', $partyServiceMock);

        $this->dispatch('/loggjafarthing/1/thingmal/3/raedur/4', 'GET');

        /** @var  $contentRange \Zend\Http\Header\ContentRange */
        $contentRange = $this->getResponse()
            ->getHeaders()
            ->get('Content-Range');

        $this->assertEquals('items 25-49/100', $contentRange->getFieldValue());
    }

    public function testPutSuccess()
    {
        $pdoMock = Mockery::mock('PDO');
        $serviceMock = Mockery::mock('Althingi\Service\Speech')
            ->shouldReceive('create')
            ->andReturnUsing(function ($object) {
                $this->assertEquals(20, $object->plenary_id);
                $this->assertEquals(10, $object->congressman_id);
                $this->assertEquals('*', $object->iteration);
                $this->assertEquals(1, $object->assembly_id);
                $this->assertEquals(3, $object->issue_id);
                $this->assertEquals(4, $object->speech_id);
                return 10;
            })
            ->once()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Speech', $serviceMock);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/1/thingmal/3/raedur/4', 'PUT', [
            'from' => '2001-01-01 00:00:00',
            'to' => '2001-01-01 00:00:00',
            'plenary_id' => 20,
            'congressman_id' => 10,
            'congressman_type' => null,
            'iteration' => '*',
            'type' => 't1',
            'text' => 't2'
        ]);

        $this->assertControllerClass('SpeechController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    public function testPutInvalidForm()
    {
        $pdoMock = Mockery::mock('PDO');
        $serviceMock = Mockery::mock('Althingi\Service\Speech')
            ->shouldReceive('create')
            ->andReturn(null)
            ->never()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Speech', $serviceMock);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/1/thingmal/3/raedur/4', 'PUT', [
            'plenary_id' => 20,
            'congressman_id' => 10,
            'congressman_type' => null,
            'iteration' => '*',
            'type' => 't1',
            'text' => 't2'
        ]);

        $this->assertControllerClass('SpeechController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    public function testGetList()
    {
        $pdoMock = Mockery::mock('PDO');
        $serviceMock = Mockery::mock('Althingi\Service\Speech')
            ->shouldReceive('fetchByIssue')
            ->andReturnUsing(function ($assembly, $issue) {
                $this->assertEquals(144, $assembly);
                $this->assertEquals(3, $issue);
                return [];
            })
            ->once()
            ->getMock()
        ->shouldReceive('countByIssue')
        ->andReturn(100)
        ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Speech', $serviceMock);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/144/thingmal/3/raedur');

        $this->assertControllerClass('SpeechController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    public function testPatchSuccess()
    {
        $pdoMock = Mockery::mock('PDO');
        $speechServiceMock = Mockery::mock('Althingi\Service\Speech')
            ->shouldReceive('get')
            ->andReturnUsing(function ($speechId) {
                $this->assertEquals(4, $speechId);
                return (object)[];
            })
            ->getMock()
            ->shouldReceive('update')
            ->andReturn()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Speech', $speechServiceMock);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/144/thingmal/3/raedur/4', 'PATCH', [
            'speech_id' => 4,
            'to' => '2000-01-01 00:00:01',
            'from' => '2000-01-01 00:00:00',
            'plenary_id' => 1,
            'assembly_id' => 145,
            'issue_id' => 1,
            'congressman_id' => 1,
        ]);

        $this->assertResponseStatusCode(204);
    }

    public function testPatchInvalid()
    {
        $pdoMock = Mockery::mock('PDO');
        $speechServiceMock = Mockery::mock('Althingi\Service\Speech')
            ->shouldReceive('get')
            ->andReturn((object) [])
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Speech', $speechServiceMock);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/144/thingmal/3/raedur/4', 'PATCH', [
            'speech_id' => 4,
        ]);

        $this->assertResponseStatusCode(400);
    }

    public function testPatchNotFound()
    {
        $pdoMock = Mockery::mock('PDO');
        $speechServiceMock = Mockery::mock('Althingi\Service\Speech')
            ->shouldReceive('get')
            ->andReturn(null)
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Speech', $speechServiceMock);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/144/thingmal/3/raedur/4', 'PATCH');

        $this->assertResponseStatusCode(404);
    }

    public function testOptions()
    {
        $pdoMock = Mockery::mock('\PDO');

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/144/thingmal/3/raedur/4', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS', 'PUT', 'PATCH',];
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }

    public function testOptionsList()
    {
        $pdoMock = Mockery::mock('\PDO');

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/144/thingmal/3/raedur', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS'];
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }
}
