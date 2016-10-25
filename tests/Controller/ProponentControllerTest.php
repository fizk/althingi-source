<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ProponentControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );
        parent::setUp();
    }

    public function testPutSuccess()
    {
        $pdoMock = \Mockery::mock('PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->shouldReceive('execute')
            ->andReturn(new \stdClass())
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/145/thingmal/2/thingskjal/637/flutningsmenn/1018', 'PUT', [
            'order' => '1',
        ]);

        $this->assertControllerClass('ProponentController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    public function testPatchSuccess()
    {
        $pdoMock = \Mockery::mock('PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->shouldReceive('fetchObject')
            ->andReturn((object)[
                'assembly_id' => 145,
                'issue_id' => 2,
                'document_id' => 637,
                'congressman_id' => 1018,
                'order' => '1',
            ])
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/145/thingmal/2/thingskjal/637/flutningsmenn/1018', 'PATCH', [
            'date' => '2001-01-01',
            'type' => 'some type'
        ]);

        $this->assertControllerClass('ProponentController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(204);
    }
}
