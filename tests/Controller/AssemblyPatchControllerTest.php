<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 6/06/15
 * Time: 10:28 PM
 */

namespace Althingi\Controller;

use Zend\Stdlib\Parameters;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class AssemblyPatchControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );
        parent::setUp();
    }

    public function testPassingInArgument()
    {
        $pdoMock = \Mockery::mock('PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->getMock()

            ->shouldReceive('execute')
            ->andReturnSelf()
            ->getMock()

            ->shouldReceive('fetchObject')
            ->andReturn((object) ['assembly_id' => 1])
            ->getMock()

            ->shouldReceive('rowCount')
            ->andReturn(1)
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/1', 'PATCH', [
            'to' => '2001-01-01',
            'from' => '2000-01-01',
        ]);
        $c = $this->getResponse()->getContent();
        $this->assertResponseStatusCode(205);
    }

    public function testPatchResourceNotFound()
    {
        $pdoMock = \Mockery::mock('PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->getMock()

            ->shouldReceive('execute')
            ->andReturnSelf()
            ->getMock()

            ->shouldReceive('fetchObject')
            ->andReturn(false)
            ->getMock();


        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/1', 'PATCH');
        $this->assertResponseStatusCode(404);
    }


    public function testPatchInvalid()
    {
        $pdoMock = \Mockery::mock('PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->getMock()

            ->shouldReceive('execute')
            ->andReturnSelf()
            ->getMock()

            ->shouldReceive('fetchObject')
            ->andReturn((object) ['assembly_id' => 1])
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/1', 'PATCH', [
            'from' => 'invalid-date'
        ]);
        $this->assertResponseStatusCode(400);
    }
}
