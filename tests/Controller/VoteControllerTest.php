<?php

namespace Althingi\Controller;

use Althingi\{Model, Service};
use Althingi\Controller\VoteController;
use Althingi\ServiceHelper;
use Library\Container\Container;
use Mockery;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(VoteController::class)]
#[CoversMethod(VoteController::class, 'setVoteService')]
#[CoversMethod(VoteController::class, 'get')]
#[CoversMethod(VoteController::class, 'getList')]
#[CoversMethod(VoteController::class, 'options')]
#[CoversMethod(VoteController::class, 'optionsList')]
#[CoversMethod(VoteController::class, 'patch')]
#[CoversMethod(VoteController::class, 'put')]
class VoteControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Vote::class,
        ]);
    }

    #[After]
    public function down(): void
    {
        $this->destroyServices();
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function getSuccessful()
    {
        $this->getMockService(Service\Vote::class)
            ->shouldReceive('get')
            ->with(3)
            ->andReturn((new Model\Vote())->setKind(Model\KindEnum::A))
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3', 'GET');

        $this->assertControllerName(VoteController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getResourceNotFound()
    {
        $this->getMockService(Service\Vote::class)
            ->shouldReceive('get')
            ->with(3)
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3', 'GET');

        $this->assertControllerName(VoteController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function getList()
    {
        $this->getMockService(Service\Vote::class)
            ->shouldReceive('fetchByIssue')
            ->with(1, 2)
            ->andReturn([(new Model\Vote())->setKind(Model\KindEnum::A)])
            ->once()
            ->getMock()
        ;

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur', 'GET');

        $this->assertControllerName(VoteController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function putSuccess()
    {
        $this->getMockService(Service\Vote::class)
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3', 'PUT', [
            'date' => '2001-01-01 00:00:00',
            'type' => 'nei',
            'method' => 'nei',
        ]);

        $this->assertControllerName(VoteController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function putInvalid()
    {
        $this->getMockService(Service\Vote::class)
            ->shouldReceive('create')
            ->never()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3', 'PUT', [
            'type' => 'nei',
            'method' => 'nei',
        ]);

        $this->assertControllerName(VoteController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    #[Test]
    public function patchSuccess()
    {
        $returnedData = (new Model\Vote())
            ->setVoteId(3)
            ->setIssueId(2)
            ->setAssemblyId(1)
            ->setDate(new \DateTime('2000-01-01 00:00:00'))
            ->setType('type')
            ->setKind(Model\KindEnum::A)
            ->setMethod('method');

        $expectedData = (new Model\Vote())
            ->setVoteId(3)
            ->setIssueId(2)
            ->setAssemblyId(1)
            ->setKind(Model\KindEnum::A)
            ->setDate(new \DateTime('2001-01-01 01:02:03'))
            ->setType('type')
            ->setMethod('method');

        $this->getMockService(Service\Vote::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn($returnedData)
            ->getMock()

            ->shouldReceive('update')
            ->with(Mockery::on(function ($actualData) use ($expectedData) {
                return $actualData == $expectedData;
            }))
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3', 'PATCH', [
            'date' => '2001-01-01 01:02:03',
        ]);

        $this->assertControllerName(VoteController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    #[Test]
    public function optionsSuccessful()
    {
        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3', 'OPTIONS');

        $this->assertControllerName(VoteController::class);
        $this->assertActionName('options');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function optionsList()
    {
        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur', 'OPTIONS');

        $this->assertControllerName(VoteController::class);
        $this->assertActionName('optionsList');
        $this->assertResponseStatusCode(200);
    }
}
