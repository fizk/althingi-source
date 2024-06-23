<?php

namespace Althingi\Controller;

use Althingi\Controller\VoteItemController;
use Althingi\Model;
use Althingi\Model\VoteItemAndAssemblyIssue;
use Althingi\Service;
use Althingi\Service\Congressman;
use Althingi\Service\Constituency;
use Althingi\Service\Party;
use Althingi\Service\Vote;
use Althingi\Service\VoteItem;
use Althingi\ServiceHelper;
use DateTime;
use Library\Container\Container;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Class VoteItemControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\VoteItemController
 *
 * @covers \Althingi\Controller\VoteItemController::setVoteItemService
 * @covers \Althingi\Controller\VoteItemController::setVoteService
 * @covers \Althingi\Controller\VoteItemController::setCongressmanService
 * @covers \Althingi\Controller\VoteItemController::setPartyService
 * @covers \Althingi\Controller\VoteItemController::setConstituencyService
 */
class VoteItemControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            VoteItem::class,
            Vote::class,
            Constituency::class,
            Congressman::class,
            Party::class,
        ]);
    }

    public function tearDown(): void
    {
        $this->destroyServices();
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @covers ::getList
     * @throws \Exception
     */
    public function testGetList()
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
            ->andReturn(
                (new Model\Party())
                    ->setPartyId(1)
                    ->setName('name')
            )
            ->once()
            ->getMock();

        $this->getMockService(Service\Constituency::class)
            ->shouldReceive('getByCongressman')
            ->andReturn(
                (new Model\ConstituencyDate())
                    ->setConstituencyId(1)
            )
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3/atkvaedi');
        $this->assertControllerName(VoteItemController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }


    /**
     * @covers ::post
     */
    public function testPostSuccess()
    {
        $this->getMockService(VoteItem::class)
            ->shouldReceive('create')
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3/atkvaedi', 'POST', [
            'congressman_id' => 1,
            'vote' => 'nei'
        ]);

        $this->assertControllerName(VoteItemController::class);
        $this->assertActionName('post');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::post
     */
    public function testPostUpdate()
    {
        $exception = new \PDOException();
        $exception->errorInfo = ['', 1062, ''];

        $this->getMockService(VoteItem::class)
            ->shouldReceive('create')
            ->andThrow($exception)
            ->once()
            ->getMock()
            ->shouldReceive('getByVote')
            ->with(3, 1)
            ->andReturn((new VoteItemAndAssemblyIssue())->setAssemblyId(1)->setIssueId(2)->setVoteId(3))
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
    /**
     * @covers ::post
     */
    public function testPostDifferentError()
    {
        $exception = new \PDOException();
        $exception->errorInfo = ['', 1234, ''];

        $this->getMockService(VoteItem::class)
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

    /**
     * @covers ::post
     */
    public function testPostInvalidParams()
    {
        $this->getMockService(VoteItem::class)
            ->shouldReceive('create')
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3/atkvaedi', 'POST');

        $this->assertControllerName(VoteItemController::class);
        $this->assertActionName('post');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatch()
    {
        $expectedObject = (new \Althingi\Model\VoteItem())
            ->setCongressmanId(1)
            ->setVote('no')
            ->setVoteId(3)
            ->setVoteItemId(30);

        $this->getMockService(VoteItem::class)
            ->shouldReceive('get')
            ->with(30)
            ->once()
            ->andReturn(
                (new \Althingi\Model\VoteItem())
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

    /**
     * @covers ::patch
     */
    public function testPatchNotFound()
    {
        $this->getMockService(VoteItem::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3/atkvaedi/30', 'PATCH');

        $this->assertControllerName(VoteItemController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }
}
