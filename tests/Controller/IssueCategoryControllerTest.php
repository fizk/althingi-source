<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IssueCategoryControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );
        parent::setUp();
    }

    public function testPut()
    {
        $pdoMockery = \Mockery::mock('PDO')
            ->shouldReceive('lastInsertId')
            ->andReturn(1)
            ->getMock()
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('execute')
            ->andReturnSelf(1)
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMockery);

        $this->dispatch('/loggjafarthing/141/thingmal/131/efnisflokkar/21', 'PUT');
        $this->assertControllerClass('IssueCategoryController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    public function testPatch()
    {
        $pdoMockery = \Mockery::mock('PDO')
            ->shouldReceive('rowCount')
            ->andReturn(1)
            ->getMock()
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('fetchObject')
            ->andReturn((object) [
                'assembly_id' => 141,
                'issue_id' => 131,
                'category_id' => 21,
            ])
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMockery);

        $this->dispatch('/loggjafarthing/141/thingmal/131/efnisflokkar/21', 'PATCH');
        $this->assertControllerClass('IssueCategoryController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    public function testPatchNotFound()
    {
        $pdoMockery = \Mockery::mock('PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('fetchObject')
            ->andReturn(null)
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMockery);

        $this->dispatch('/loggjafarthing/141/thingmal/131/efnisflokkar/21', 'PATCH');
        $this->assertControllerClass('IssueCategoryController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }
}
