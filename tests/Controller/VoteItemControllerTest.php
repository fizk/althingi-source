<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Althingi\Model\VoteItemAndAssemblyIssue;
use Althingi\Service\VoteItem;
use Mockery;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class VoteItemControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            VoteItem::class,
        ]);
    }

    public function tearDown()
    {
        $this->destroyServices();
        Mockery::close();
        return parent::tearDown();
    }

    public function testPostSuccess()
    {
        $this->getMockService(VoteItem::class)
            ->shouldReceive('create')
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/2/atkvaedagreidslur/3/atkvaedi', 'POST', [
            'congressman_id' => 1,
            'vote' => 'nei'
        ]);

        $this->assertControllerClass('VoteItemController');
        $this->assertActionName('post');
        $this->assertResponseStatusCode(201);
    }

    public function testPostUpdate()
    {
        $this->getMockService(VoteItem::class)
            ->shouldReceive('create')
            ->andThrow(new \PDOException('', 23000))
            ->once()
            ->getMock()
            ->shouldReceive('getByVote')
            ->with(3, 1)
            ->andReturn((new VoteItemAndAssemblyIssue())->setAssemblyId(1)->setIssueId(2)->setVoteId(3))
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/2/atkvaedagreidslur/3/atkvaedi', 'POST', [
            'congressman_id' => 1,
            'vote' => 'nei'
        ]);

        $this->assertControllerClass('VoteItemController');
        $this->assertActionName('post');
        $this->assertResponseStatusCode(409);
    }

    public function testPostInvalidParams()
    {
        $this->getMockService(VoteItem::class)
            ->shouldReceive('create')
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/2/atkvaedagreidslur/3/atkvaedi', 'POST');

        $this->assertControllerClass('VoteItemController');
        $this->assertActionName('post');
        $this->assertResponseStatusCode(400);
    }

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

        $this->dispatch('/loggjafarthing/1/thingmal/2/atkvaedagreidslur/3/atkvaedi/30', 'PATCH', [
            'vote' => 'no'
        ]);

        $this->assertControllerClass('VoteItemController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    public function testPatchNotFound()
    {
        $this->getMockService(VoteItem::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/2/atkvaedagreidslur/3/atkvaedi/30', 'PATCH');

        $this->assertControllerClass('VoteItemController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }
}
