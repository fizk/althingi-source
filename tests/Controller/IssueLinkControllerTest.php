<?php

namespace Althingi\Controller;

use Althingi\{Model, Service};
use Althingi\Controller;
use Althingi\ServiceHelper;
use Library\Container\Container;
use Mockery;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(IssueLinkController::class)]
#[CoversMethod(IssueLinkController::class, 'setIssueLinkService')]
#[CoversMethod(IssueLinkController::class, 'get')]
#[CoversMethod(IssueLinkController::class, 'put')]
class IssueLinkControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\IssueLink::class,
        ]);
    }

    #[After]
    public function down(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function getSuccessful()
    {
        $this->getMockService(Service\IssueLink::class)
            ->shouldReceive('fetchAll')
            ->with(100, 200, Model\KindEnum::A)
            ->andReturn([new Model\Issue()])
            ->getMock();

        $this->dispatch('/loggjafarthing/100/thingmal/a/200/tengdmal', 'GET');

        $this->assertControllerName(Controller\IssueLinkController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function putSuccessfullCreate()
    {
        $expectedModel = (new Model\IssueLink())
            ->setFromAssemblyId(100)
            ->setFromIssueId(200)
            ->setFromKind(Model\KindEnum::A)
            ->setAssemblyId(1)
            ->setIssueId(2)
            ->setKind(Model\KindEnum::A)
            ->setType('some');

        $this->getMockService(Service\IssueLink::class)
            ->shouldReceive('save')
            ->withArgs(fn ($actualModel) => $expectedModel == $actualModel)
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/100/thingmal/a/200/tengdmal', 'PUT', [
            'to_assembly_id' => '1',
            'to_issue_id' => '2',
            'to_kind' => 'A',
            'type' => 'some'
        ]);

        $this->assertControllerName(Controller\IssueLinkController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function putSuccessfullUpdate()
    {
        $expectedModel = (new Model\IssueLink())
            ->setFromAssemblyId(100)
            ->setFromIssueId(200)
            ->setFromKind(Model\KindEnum::A)
            ->setAssemblyId(1)
            ->setIssueId(2)
            ->setKind(Model\KindEnum::A)
            ->setType('some');

        $this->getMockService(Service\IssueLink::class)
            ->shouldReceive('save')
            ->withArgs(fn ($actualModel) => $expectedModel == $actualModel)
            ->andReturn(0)
            ->getMock();

        $this->dispatch('/loggjafarthing/100/thingmal/a/200/tengdmal', 'PUT', [
            'to_assembly_id' => '1',
            'to_issue_id' => '2',
            'to_kind' => 'A',
            'type' => 'some'
        ]);

        $this->assertControllerName(Controller\IssueLinkController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(205);
    }

    #[Test]
    public function putInvalidFormError()
    {
        $expectedModel = (new Model\IssueLink())
            ->setFromAssemblyId(100)
            ->setFromIssueId(200)
            ->setFromKind(Model\KindEnum::A)
            ->setAssemblyId(1)
            ->setIssueId(2)
            ->setKind(Model\KindEnum::A)
            ->setType('some');

        $this->getMockService(Service\IssueLink::class)
            ->shouldReceive('save')
            ->withArgs(fn ($actualModel) => $expectedModel == $actualModel)
            ->andReturn(0)
            ->getMock();

        $this->dispatch('/loggjafarthing/100/thingmal/a/200/tengdmal', 'PUT', [
            'to_assembly_id' => '1',
        ]);

        $this->assertControllerName(Controller\IssueLinkController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }
}
