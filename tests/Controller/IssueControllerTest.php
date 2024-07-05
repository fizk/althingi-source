<?php

namespace Althingi\Controller;

use Althingi\{Model, Service};
use Althingi\Controller;
use Althingi\Model\KindEnum;
use Althingi\ServiceHelper;
use Library\Container\Container;
use Mockery;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(IssueController::class)]
#[CoversMethod(IssueController::class, 'setIssueService')]
#[CoversMethod(IssueController::class, 'get')]
#[CoversMethod(IssueController::class, 'getList')]
#[CoversMethod(IssueController::class, 'options')]
#[CoversMethod(IssueController::class, 'optionsList')]
#[CoversMethod(IssueController::class, 'patch')]
#[CoversMethod(IssueController::class, 'put')]
class IssueControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
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

    #[After]
    public function down(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function getSuccessA()
    {
        $this->getMockService(Service\Issue::class)
            ->shouldReceive('get')
            ->with(200, 100, Model\KindEnum::A)
            ->andReturn(new Model\Issue())
            ->getMock();

        $this->dispatch('/loggjafarthing/100/thingmal/a/200', 'GET');

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getSuccessB()
    {
        $this->getMockService(Service\Issue::class)
            ->shouldReceive('get')
            ->with(200, 100, Model\KindEnum::B)
            ->andReturn(new Model\Issue())
            ->getMock();

        $this->dispatch('/loggjafarthing/100/thingmal/b/200', 'GET');

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getNotFound()
    {
        $this->getMockService(Service\Issue::class)
            ->shouldReceive('get')
            ->with(200, 100, Model\KindEnum::A)
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/100/thingmal/a/200', 'GET');

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function getList()
    {
        $this->getMockService(Service\Issue::class)
            ->shouldReceive('fetchByAssembly')
            ->with(100, 0, null, null, [], [], [KindEnum::A, KindEnum::B])
            ->andReturn(array_map(function () {
                return new Model\Issue();
            }, range(0, 24)))
            ->getMock();

        $this->dispatch('/loggjafarthing/100/thingmal', 'GET');

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function putSuccess()
    {
        $expectedObject = (new Model\Issue())
            ->setIssueId(200)
            ->setAssemblyId(100)
            ->setKind(Model\KindEnum::A)
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
            'kind' => Model\KindEnum::A->value,
            'type' => '1',
            'type_name' => 'tn',
            'type_subname' => 'tsn',
        ]);

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function putInvalidForm()
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

    #[Test]
    public function patchSuccessful()
    {
        $expectedObject = (new Model\Issue())
            ->setIssueId(200)
            ->setAssemblyId(100)
            ->setName('n1')
            ->setKind(Model\KindEnum::A)
            ->setType('1')
            ->setTypeName('tn')
            ->setTypeSubname('tsn')
        ;

        $this->getMockService(Service\Issue::class)
            ->shouldReceive('get')
            ->once()
            ->with(200, 100, Model\KindEnum::A)
            ->andReturn((new Model\Issue())->setIssueId(200)->setAssemblyId(100)->setKind(Model\KindEnum::A))
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

    #[Test]
    public function optionsList()
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

    #[Test]
    public function optionsSuccessful()
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
