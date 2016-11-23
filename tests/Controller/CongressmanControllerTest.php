<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 24/04/2016
 * Time: 3:36 PM
 */

namespace Althingi\Controller;

use Mockery;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class CongressmanControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );
        parent::setUp();
    }

    public function testGetList()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('fetchColumn')
            ->andReturn(100)
            ->mock()
            ->shouldReceive('fetchAll')
            ->andReturn([])
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/thingmenn', 'GET');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('getList');
    }

    public function testGet()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('fetchObject')
            ->andReturn($this->congressman())
            ->mock()
            ->shouldReceive('fetchAll')
            ->andReturn($this->parties())
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/thingmenn/1', 'GET');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('get');
    }

    public function testGetNotFound()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('fetchObject')
            ->andReturn(null)
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/thingmenn/1', 'GET');

        $this->assertResponseStatusCode(404);
    }

    public function testPutSuccess()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('lastInsertId')
            ->andReturn(1)
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/thingmenn/1', 'PUT', [
            'name' => 'some name',
            'birth' => '1978-04-11'
        ]);

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    public function testPutInvalidData()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('lastInsertId')
            ->andReturn(1)
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/thingmenn/1', 'PUT', [
            'name' => 'some name',
            'birth' => 'not a date'
        ]);

        $this->assertResponseStatusCode(400);
    }

    public function testPatchSuccess()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('fetchObject')
            ->andReturn($this->congressman())
            ->mock()
            ->shouldReceive('rowCount')
            ->andReturn(1)
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/thingmenn/1', 'PATCH', [
            'name' => 'some name',
        ]);

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    public function testPatchInvalidData()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('fetchObject')
            ->andReturn($this->congressman())
            ->mock()
            ->shouldReceive('rowCount')
            ->andReturn(1)
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/thingmenn/1', 'PATCH', [
            'birth' => 'not a date',
        ]);

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    public function testPatchResourceNotFound()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('fetchObject')
            ->andReturn(null)
            ->mock()
            ->shouldReceive('rowCount')
            ->andReturn(1)
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/thingmenn/1', 'PATCH', [
            'birth' => 'not a date',
        ]);

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    public function testDeleteSuccess()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('fetchObject')
            ->andReturn($this->congressman())
            ->mock()
            ->shouldReceive('rowCount')
            ->andReturn(1)
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/thingmenn/1', 'DELETE');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('delete');
        $this->assertResponseStatusCode(205);
    }

    public function testDeleteResourceNotFound()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('fetchObject')
            ->andReturn(null)
            ->mock()
            ->shouldReceive('rowCount')
            ->andReturn(1)
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/thingmenn/1', 'DELETE');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('delete');
        $this->assertResponseStatusCode(404);
    }

    private function congressman()
    {
        return (object) [
            'congressman_id' => 1,
            'name' => 'some name',
            'birth' => '1978-04-11',
            'death' => null,
        ];
    }

    private function parties()
    {
        return [
            (object) ['party_id' => 1]
        ];
    }
}
