<?php

namespace Althingi\Controller;

use Althingi\{Model, Service};
use Althingi\Controller\VoteItemController;
use Althingi\ServiceHelper;
use DateTime;
use Library\Container\Container;
use Mockery;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(VoteItemController::class)]
#[CoversMethod(VoteItemController::class, 'setVoteItemService')]
#[CoversMethod(VoteItemController::class, 'setVoteService')]
#[CoversMethod(VoteItemController::class, 'setCongressmanService')]
#[CoversMethod(VoteItemController::class, 'setPartyService')]
#[CoversMethod(VoteItemController::class, 'setConstituencyService')]
#[CoversMethod(VoteItemController::class, 'getList')]
#[CoversMethod(VoteItemController::class, 'patch')]
#[CoversMethod(VoteItemController::class, 'post')]
class VoteItemControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\VoteItem::class,
            Service\Vote::class,
            Service\Constituency::class,
            Service\Congressman::class,
            Service\Party::class,
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
    public function getList()
    {
        $this->getMockService(Service\Vote::class)
            ->shouldReceive('get')
            ->with(3)
            ->andReturn(
                (new Model\Vote())
                    ->setVoteId(3)
                    ->setDate(new DateTime('2001-01-01'))
            )
            ->once()
            ->getMock();

        $this->getMockService(Service\VoteItem::class)
            ->shouldReceive('fetchByVote')
            ->with(3)
            ->andReturn([
                (new Model\VoteItem())
                    ->setVoteId(3)
                    ->setVote('yes')
                    ->setVoteItemId(1)
                    ->setCongressmanId(101)

            ])
            ->once()
            ->getMock();

        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->with(101)
            ->andReturn(new Model\Congressman())
            ->once()
            ->getMock();

        $this->getMockService(Service\Party::class)
            ->shouldReceive('getByCongressman')
            ->once()
            ->andReturn(
                (new Model\Party())
                    ->setPartyId(1)
                    ->setName('name')
            )
            ->getMock();

        $this->getMockService(Service\Constituency::class)
            ->shouldReceive('getByCongressman')
            ->once()
            ->andReturn(
                (new Model\ConstituencyDate())
                    ->setConstituencyId(1)
            )
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3/atkvaedi');
        $this->assertControllerName(VoteItemController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function postSuccess()
    {
        $this->getMockService(Service\VoteItem::class)
            ->shouldReceive('create')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3/atkvaedi', 'POST', [
            'congressman_id' => 1,
            'vote' => 'nei'
        ]);

        $this->assertControllerName(VoteItemController::class);
        $this->assertActionName('post');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function postUpdate()
    {
        $exception = new \PDOException();
        $exception->errorInfo = ['', 1062, ''];

        $this->getMockService(Service\VoteItem::class)
            ->shouldReceive('create')
            ->andThrow($exception)
            ->once()
            ->getMock()
            ->shouldReceive('getByVote')
            ->with(3, 1)
            ->andReturn((new Model\VoteItemAndAssemblyIssue())->setAssemblyId(1)->setIssueId(2)->setVoteId(3))
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3/atkvaedi', 'POST', [
            'congressman_id' => 1,
            'vote' => 'nei'
        ]);

        $this->assertControllerName(VoteItemController::class);
        $this->assertActionName('post');
        $this->assertResponseStatusCode(409);
    }

    #[Test]
    public function postDifferentError()
    {
        $exception = new \PDOException();
        $exception->errorInfo = ['', 1234, ''];

        $this->getMockService(Service\VoteItem::class)
            ->shouldReceive('create')
            ->andThrow($exception)
            ->once()
            ->getMock()
            ->shouldReceive('getByVote')
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3/atkvaedi', 'POST', [
            'congressman_id' => 1,
            'vote' => 'nei'
        ]);

        $this->assertControllerName(VoteItemController::class);
        $this->assertActionName('post');
        $this->assertResponseStatusCode(500);
    }

    #[Test]
    public function postInvalidParams()
    {
        $this->getMockService(Service\VoteItem::class)
            ->shouldReceive('create')
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3/atkvaedi', 'POST');

        $this->assertControllerName(VoteItemController::class);
        $this->assertActionName('post');
        $this->assertResponseStatusCode(400);
    }

    #[Test]
    public function patchSuccessful()
    {
        $expectedObject = (new Model\VoteItem())
            ->setCongressmanId(1)
            ->setVote('no')
            ->setVoteId(3)
            ->setVoteItemId(30);

        $this->getMockService(Service\VoteItem::class)
            ->shouldReceive('get')
            ->with(30)
            ->once()
            ->andReturn(
                (new Model\VoteItem())
                ->setCongressmanId(1)
                ->setVote('yes')
                ->setVoteId(3)
                ->setVoteItemId(30)
            )
            ->getMock()
        ->shouldReceive('update')
            ->with(\Mockery::on(function ($actualData) use ($expectedObject) {
                return $actualData == $expectedObject;
            }))
        ->andReturn(1)
        ->once()
        ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3/atkvaedi/30', 'PATCH', [
            'vote' => 'no'
        ]);

        $this->assertControllerName(VoteItemController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    #[Test]
    public function patchNotFound()
    {
        $this->getMockService(Service\VoteItem::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3/atkvaedi/30', 'PATCH');

        $this->assertControllerName(VoteItemController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }
}
