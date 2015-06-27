<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IssueControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );
        parent::setUp();
    }

    public function testPutSuccess()
    {
        $serviceMock = \Mockery::mock('Althingi\Service\Issue')
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

        $this->dispatch('/api/loggjafarthing/100/thingmal/200', 'PUT', [
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

        $this->dispatch('/api/loggjafarthing/100/thingmal/200', 'PUT', [
            'type_name' => 'tn',
            'type_subname' => 'tsn',
        ]);

        $this->assertControllerClass('IssueController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    public function testGetList()
    {
        $serviceMock = \Mockery::mock('Althingi\Service\Issue')
            ->shouldReceive('fetchByAssembly')
                ->andReturn(array_fill(0, 25, new \stdClass()))
                ->never()
                ->getMock()
            ->shouldReceive('countByAssembly')
                ->andReturn(123)
                ->once()
                ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Issue', $serviceMock);

        $this->dispatch('/api/loggjafarthing/100/thingmal', 'GET');
        $this->assertControllerClass('IssueController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
        $this->assertResponseHeaderContains('Range-Unit', 'items');
        $this->assertResponseHeaderContains('Content-Range', 'items 0-25/123');
    }

    public function testPatch()
    {
        $serviceMock = \Mockery::mock('Althingi\Service\Issue')
            ->shouldReceive('get')
                ->andReturn($this->buildIssueObject())
                ->getMock()
            ->shouldReceive('update')
            ->andReturnUsing(function ($object) {
                $this->assertObjectNotHasAttribute('foreman', $object);
                $this->assertObjectNotHasAttribute('speakers', $object);
                $this->assertObjectHasAttribute('congressman_id', $object);
                $this->assertEquals(200, $object->congressman_id);
                $this->assertEquals('n1', $object->name);
                $this->assertEquals('A', $object->category);
            })->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Issue', $serviceMock);

        $this->dispatch('/api/loggjafarthing/100/thingmal/200', 'PATCH', [
            'name' => 'n1'
        ]);
        $this->assertControllerClass('IssueController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(200);
    }

    public function testPatchEntryNotFound()
    {
        $serviceMock = \Mockery::mock('Althingi\Service\Issue')
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

        $this->dispatch('/api/loggjafarthing/100/thingmal/200', 'PATCH', [
            'name' => 'n1'
        ]);

        $this->assertControllerClass('IssueController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    public function testPatchInvalidForm()
    {
        $serviceMock = \Mockery::mock('Althingi\Service\Issue')
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

        $this->dispatch('/api/loggjafarthing/100/thingmal/200', 'PATCH', [
            'name' => 'n1',
            'congressman_id' => 'I can not be a string'
        ]);
        $this->assertControllerClass('IssueController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    public function testGetSuccess()
    {
        $serviceMock = \Mockery::mock('Althingi\Service\Issue')
            ->shouldReceive('get')
            ->andReturn($this->buildIssueObject())
            ->once()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Issue', $serviceMock);

        $this->dispatch('/api/loggjafarthing/100/thingmal/200', 'GET');

        $this->assertControllerClass('IssueController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    public function testGetResourceNotFound()
    {
        $serviceMock = \Mockery::mock('Althingi\Service\Issue')
            ->shouldReceive('get')
            ->andReturn(null)
            ->once()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Issue', $serviceMock);

        $this->dispatch('/api/loggjafarthing/100/thingmal/200', 'GET');

        $this->assertControllerClass('IssueController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    private function buildIssueObject()
    {
        return (object) [
            "issue_id"  => 2,
            "assembly_id"  => 144,
            "category"  => "A",
            "name"  => "Virðisaukaskattur o.fl.",
            "type"  => 1,
            "type_name"  => "Frumvarp til laga",
            "type_subname"  => "lagafrumvarp",
            "status" => "Samþykkt sem lög frá Alþingi.",
            "foreman" => (object) [
                'congressman_id' => 200
            ],
            "speakers" => []
        ];
    }
}
