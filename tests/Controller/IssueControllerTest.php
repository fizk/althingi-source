<?php

namespace Althingi\Controller;

use Althingi\Controller;
use Althingi\Model;
use Althingi\Model\KindEnum;
use Althingi\Service;
use Althingi\ServiceHelper;
use Library\Container\Container;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Class IssueControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\IssueController
 *
 * @covers \Althingi\Controller\IssueController::setIssueService
 */
class IssueControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Issue::class,
            Service\Assembly::class,
            Service\Category::class,
        ]);
    }

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * @covers ::get
     */
    public function testGetSuccessA()
    {
        $this->getMockService(Service\Issue::class)
            ->shouldReceive('get')
            ->with(200, 100, KindEnum::A)
            ->andReturn(new Model\Issue())
            ->getMock();

        $this->dispatch('/loggjafarthing/100/thingmal/a/200', 'GET');

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetSuccessB()
    {
        $this->getMockService(Service\Issue::class)
            ->shouldReceive('get')
            ->with(200, 100, KindEnum::B)
            ->andReturn(new Model\Issue())
            ->getMock();

        $this->dispatch('/loggjafarthing/100/thingmal/b/200', 'GET');

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Service\Issue::class)
            ->shouldReceive('get')
            ->with(200, 100, KindEnum::A)
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/100/thingmal/a/200', 'GET');

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Service\Issue::class)
            ->shouldReceive('fetchByAssembly')
            ->with(100, 0, null, null, [], [], ['A', 'B'])
            ->andReturn(array_map(function () {
                return new Model\Issue();
            }, range(0, 24)))
            ->getMock();

        $this->dispatch('/loggjafarthing/100/thingmal', 'GET');

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::put
     */
    public function testPutSuccess()
    {
        $expectedObject = (new Model\Issue())
            ->setIssueId(200)
            ->setAssemblyId(100)
            ->setKind(KindEnum::A)
            ->setName('n1')
            ->setType('1')
            ->setTypeName('tn')
            ->setTypeSubname('tsn')
        ;

        $this->getMockService(Service\Issue::class)
            ->shouldReceive('save')
            ->with(Mockery::on(function ($actualObject) use ($expectedObject) {
                return $expectedObject == $actualObject;
            }))
            ->andReturn(1)
            ->once()
            ->getMock()
        ;

        $this->dispatch('/loggjafarthing/100/thingmal/a/200', 'PUT', [
            'name' => 'n1',
            'kind' => KindEnum::A->value,
            'type' => '1',
            'type_name' => 'tn',
            'type_subname' => 'tsn',
        ]);

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::put
     */
    public function testPutInvalidForm()
    {
        $this->getMockService(Service\Issue::class)
            ->shouldReceive('create')
            ->never()
            ->getMock()
        ;

        $this->dispatch('/loggjafarthing/100/thingmal/a/200', 'PUT', [
            'type_name' => 'tn',
            'type_subname' => 'tsn',
        ]);

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatch()
    {
        $expectedObject = (new Model\Issue())
            ->setIssueId(200)
            ->setAssemblyId(100)
            ->setName('n1')
            ->setKind(KindEnum::A)
            ->setType('1')
            ->setTypeName('tn')
            ->setTypeSubname('tsn')
        ;

        $this->getMockService(Service\Issue::class)
            ->shouldReceive('get')
            ->once()
            ->with(200, 100, KindEnum::A)
            ->andReturn((new Model\Issue())->setIssueId(200)->setAssemblyId(100)->setKind(KindEnum::A))
            ->getMock()

            ->shouldReceive('update')
            ->with(Mockery::on(function ($actualObject) use ($expectedObject) {
                return $expectedObject == $actualObject;
            }))
            ->andReturn(1)
            ->once()
            ->getMock()
        ;

        $this->dispatch('/loggjafarthing/100/thingmal/a/200', 'PATCH', [
            'name' => 'n1',
            'kind' => 'A',
            'type' => '1',
            'type_name' => 'tn',
            'type_subname' => 'tsn',
        ]);

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::optionsList
     */
    public function testOptionsList()
    {
        $this->dispatch('/loggjafarthing/100/thingmal', 'OPTIONS');

        $allows = $this->getResponse()->getHeader('Allow');
        $expectedMethods = ['GET', 'OPTIONS'];
        $actualMethods = array_map(function ($item) {
            return trim($item);
        }, explode(',', $allows[0]));

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('optionsList');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::options
     */
    public function testOptions()
    {
        $this->dispatch('/loggjafarthing/100/thingmal/a/200', 'OPTIONS');

        $allows = $this->getResponse()->getHeader('Allow');
        $expectedMethods = ['GET', 'OPTIONS', 'PUT', 'PATCH'];
        $actualMethods = array_map(function ($item) {
            return trim($item);
        }, explode(',', $allows[0]));

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('options');
        $this->assertResponseStatusCode(200);
        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }
}
