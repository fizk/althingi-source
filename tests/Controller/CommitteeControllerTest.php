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

class CommitteeControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            'Althingi\Service\Committee',
        ]);
    }

    public function tearDown()
    {
        $this->destroyServices();
        \Mockery::close();
        return parent::tearDown();
    }

    public function testGet()
    {
        $this->getMockService('Althingi\Service\Committee')
            ->shouldReceive('get')
            ->once()
            ->with(1)
            ->andReturn(new \stdClass())
            ->getMock();

        $this->dispatch('/nefndir/1', 'GET');

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    public function testGetNotFound()
    {
        $this->getMockService('Althingi\Service\Committee')
            ->shouldReceive('get')
            ->once()
            ->with(1)
            ->andReturnNull()
            ->getMock();

        $this->dispatch('/nefndir/1', 'GET');

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
    }

    public function testGetList()
    {
        $this->getMockService('Althingi\Service\Committee')
            ->shouldReceive('fetchAll')
            ->once()
            ->andReturn([])
            ->getMock();

        $this->dispatch('/nefndir', 'GET');

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
    }

    public function testPut()
    {
        $this->getMockService('Althingi\Service\Committee')
            ->shouldReceive('create')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/nefndir/1', 'PUT', [
            'first_assembly_id' => 1,
            'last_assembly_id' => 1,
            'name' => 'name',
            'abbr_short' => 'n',
            'abbr_long' => 'na',

        ]);

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    public function testPutInvalidParameters()
    {
        $this->getMockService('Althingi\Service\Committee')
            ->shouldReceive('create')
            ->never()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/nefndir/1', 'PUT', []);

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    public function testPatch()
    {
        $this->getMockService('Althingi\Service\Committee')
            ->shouldReceive('get')
            ->once()
            ->andReturn((object)[
                'committee_id' => 1,
                'first_assembly_id' => 1,
                'last_assembly_id' => 1,
                'name' => 'name',
                'abbr_short' => 'n',
                'abbr_long' => 'na',
            ])
            ->getMock()
            ->shouldReceive('update')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/nefndir/1', 'PATCH', [
            'first_assembly_id' => 1,
        ]);

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    public function testPatchNotFound()
    {
        $this->getMockService('Althingi\Service\Committee')
            ->shouldReceive('get')
            ->once()
            ->andReturnNull()
            ->getMock()
            ->shouldReceive('update')
            ->never()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/nefndir/1', 'PATCH', [
            'first_assembly_id' => 1,
        ]);

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    public function testOptions()
    {
        $this->dispatch('/nefndir/1', 'OPTIONS');

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('options');
        $this->assertResponseStatusCode(200);
        $this->assertHasResponseHeader('Allow');
        $this->assertResponseHeaderContains('Allow', 'OPTIONS, GET, PUT, PATCH');
    }

    public function testOptionsList()
    {
        $this->dispatch('/nefndir', 'OPTIONS');

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('optionsList');
        $this->assertResponseStatusCode(200);
        $this->assertHasResponseHeader('Allow');
        $this->assertResponseHeaderContains('Allow', 'OPTIONS, GET');
    }
}
