<?php

namespace AlthingiTest\Controller;

use Althingi\Service\SuperCategory;
use AlthingiTest\ServiceHelper;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class SuperCategoryControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\SuperCategoryController
 * @covers \Althingi\Controller\SuperCategoryController::setSuperCategoryService
 */
class SuperCategoryControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            SuperCategory::class,
        ]);
    }

    public function tearDown()
    {
        \Mockery::close();
        return parent::tearDown();
    }

    /**
     * @covers ::put
     */
    public function testPutSuccess()
    {
        $expectedData = (new \Althingi\Model\SuperCategory())
            ->setSuperCategoryId(1)
            ->setTitle('n1');

        $this->getMockService(SuperCategory::class)
            ->shouldReceive('save')
            ->with(\Mockery::on(function ($actualDate) use ($expectedData) {
                return $actualDate == $expectedData;
            }))
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1', 'PUT', [
            'title' => 'n1',
        ]);

        $this->assertControllerClass('SuperCategoryController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::put
     */
    public function testPutSuccessStuff()
    {
        $this->getMockService(SuperCategory::class)
            ->shouldReceive('create')
            ->never()
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/not-a-number', 'PUT', [
            'title' => 'n1',
        ]);

        $this->assertControllerClass('SuperCategoryController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $expectedData = (new \Althingi\Model\SuperCategory())
            ->setSuperCategoryId(1)
            ->setTitle('n2');

        $serverReturnedData = (new \Althingi\Model\SuperCategory())
            ->setSuperCategoryId(1)
            ->setTitle('n1');

        $this->getMockService(SuperCategory::class)
            ->shouldReceive('update')
            ->with(\Mockery::on(function ($actualData) use ($expectedData) {
                return $actualData == $expectedData;
            }))
            ->andReturn(1)
            ->once()
            ->getMock()

            ->shouldReceive('get')
            ->once()
            ->andReturn($serverReturnedData)
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1', 'PATCH', [
            'title' => 'n2',
        ]);

        $this->assertControllerClass('SuperCategoryController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::patch
     */
    public function testPatchInvalidArgs()
    {
        $this->getMockService(SuperCategory::class)
            ->shouldReceive('update')
            ->never()
            ->getMock()

            ->shouldReceive('get')
            ->once()
            ->andReturn(new \Althingi\Model\SuperCategory())
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1', 'PATCH', [
            'title' => 'title1',
        ]);

        $this->assertControllerClass('SuperCategoryController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatchNotFound()
    {
        $this->getMockService(SuperCategory::class)
            ->shouldReceive('update')
            ->never()
            ->getMock()

            ->shouldReceive('get')
            ->once()
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1', 'PATCH', [
            'title' => 'n2',
        ]);

        $this->assertControllerClass('SuperCategoryController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }
}
