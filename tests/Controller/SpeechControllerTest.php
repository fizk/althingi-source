<?php

namespace Althingi\Controller;

use Althingi\Model;
use Althingi\Service;
use Althingi\Controller;
use Althingi\Model\Assembly;
use Althingi\ServiceHelper;
use DateTime;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Mockery;

/**
 * Class SpeechControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\SpeechController
 *
 * @covers \Althingi\Controller\SpeechController::setCongressmanService
 * @covers \Althingi\Controller\SpeechController::setPartyService
 * @covers \Althingi\Controller\SpeechController::setSpeechService
 * @covers \Althingi\Controller\SpeechController::setPlenaryService
 * @covers \Althingi\Controller\SpeechController::setConstituencyService
 */
class SpeechControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new ServiceManager(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Speech::class,
            Service\Congressman::class,
            Service\Party::class,
            Service\Plenary::class,
            Service\Constituency::class,
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
    public function testGetSuccess()
    {
        $this->getMockService(Service\Speech::class)
            ->shouldReceive('countByIssue')
            ->andReturn(100)
            ->once()
            ->getMock()

            ->shouldReceive('fetch')
            ->with(4, 1, 3, 25, 'A')
            ->andReturn([
                (new Model\SpeechAndPosition())
                    ->setPosition(1)
                    ->setCongressmanId(1)
                    ->setFrom(new \DateTime())

            ])
            ->once()
            ->getMock();

        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->andReturn(
                (new \Althingi\Model\Congressman())
                    ->setCongressmanId(1)
            )
            ->getMock();

        $this->getMockService(Service\Party::class)
            ->shouldReceive('getByCongressman')
            ->andReturn(
                (new \Althingi\Model\Party())
                    ->setPartyId(1)
                    ->setName('name')
            )
            ->getMock();

        $this->getMockService(Service\Constituency::class)
            ->shouldReceive('getByCongressman')
            ->andReturn(
                (new Model\ConstituencyDate())
                    ->setConstituencyId(1)
            )
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/3/raedur/4', 'GET');

        $this->assertControllerName(Controller\SpeechController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::get
     */
    public function testGetRangeHeaders()
    {
        $this->getMockService(Service\Speech::class)
            ->shouldReceive('countByIssue')
            ->andReturn(100)
            ->getMock()
            ->shouldReceive('fetch')
            ->andReturn(array_map(function ($i) {
                return  (new Model\SpeechAndPosition())
                    ->setCongressmanId(1)
                    ->setText('<?xml version="1.0" ?><root />')
                    ->setFrom(new \DateTime('2000-01-01'))
                    ->setPosition($i);
            }, range(25, 49)))
            ->getMock();

        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->andReturn(new \Althingi\Model\Congressman())
            ->getMock();

        $this->getMockService(Service\Party::class)
            ->shouldReceive('getByCongressman')
            ->andReturn(
                (new \Althingi\Model\Party())
                    ->setPartyId(1)
                    ->setName('name')
            )
            ->getMock();

        $this->getMockService(Service\Constituency::class)
            ->shouldReceive('getByCongressman')
            ->andReturn(
                (new Model\ConstituencyDate())
                    ->setConstituencyId(1)
            )
            ->times(25)
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/3/raedur/4', 'GET');
        $resp = $this->getResponse();
        /** @var  $contentRange \Laminas\Http\Header\ContentRange */
        $contentRange = $this->getResponse()
            ->getHeader('Content-Range');

        $this->assertEquals('items 25-49/100', $contentRange[0]);
    }

    /**
     * @covers ::put
     */
    public function testPutSuccess()
    {
        $expectedData = (new Model\Speech())
            ->setPlenaryId(20)
            ->setCongressmanId(10)
            ->setIteration('*')
            ->setAssemblyId(1)
            ->setIssueId(3)
            ->setSpeechId('20210613T012100')
            ->setFrom(new \DateTime('2001-01-01 00:00:00'))
            ->setTo(new \DateTime('2001-01-01 00:00:00'))
            ->setType('t1')
            ->setText('t2')
            ->setCategory('A')
            ->setValidated(false);

        $this->getMockService(Service\Speech::class)
            ->shouldReceive('save')
            ->with(Mockery::on(function ($actualData) use ($expectedData) {
                return $actualData == $expectedData;
            }))
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/3/raedur/20210613T012100', 'PUT', [
            'id' => '20210613T012100',
            'from' => '2001-01-01 00:00:00',
            'to' => '2001-01-01 00:00:00',
            'plenary_id' => 20,
            'assembly_id' => 1,
            'issue_id' => 3,
            'congressman_id' => 10,
            'congressman_type' => null,
            'iteration' => '*',
            'type' => 't1',
            'text' => 't2',
            'category' =>' A',
            'validated' => 'false',
        ]);

        $this->assertControllerName(Controller\SpeechController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::put
     */
    public function testPutInvalidForm()
    {
        $this->getMockService(Service\Speech::class)
            ->shouldReceive('create')
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/3/raedur/4', 'PUT', [
            'plenary_id' => 20,
            'congressman_id' => 10,
            'congressman_type' => null,
            'iteration' => '*',
            'type' => 't1',
            'text' => 't2'
        ]);

        $this->assertControllerName(Controller\SpeechController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::put
     */
    public function testPutDuplicate()
    {
        $exception = new \PDOException();
        $exception->errorInfo = ['', 1452, ''];

        $this->getMockService(Service\Speech::class)
            ->shouldReceive('save')
            ->andThrow($exception)
            ->twice()
            ->getMock();

        $this->getMockService(Service\Plenary::class)
            ->shouldReceive('save')
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/3/raedur/4', 'PUT', [
            'from' => '2001-01-01 00:00:00',
            'to' => '2001-01-01 00:00:00',
            'plenary_id' => 20,
            'congressman_id' => 10,
            'congressman_type' => null,
            'iteration' => '*',
            'type' => 't1',
            'text' => 't2',
            'validated' => 'false'
        ]);

        $this->assertControllerName(Controller\SpeechController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(500);
    }

    /**
     * @covers ::put
     */
    public function testPutSomeError()
    {
        $exception = new \PDOException();
        $exception->errorInfo = ['', 0, ''];

        $this->getMockService(Service\Speech::class)
            ->shouldReceive('save')
            ->andThrow($exception)
            ->once()
            ->getMock();

        $this->getMockService(Service\Plenary::class)
            ->shouldReceive('save')
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/3/raedur/4', 'PUT', [
            'from' => '2001-01-01 00:00:00',
            'to' => '2001-01-01 00:00:00',
            'plenary_id' => 20,
            'congressman_id' => 10,
            'congressman_type' => null,
            'iteration' => '*',
            'type' => 't1',
            'text' => 't2',
            'validated' => 'false'
        ]);

        $this->assertControllerName(Controller\SpeechController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(500);
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Service\Speech::class)
            ->shouldReceive('fetchAllByIssue')
            ->with(144, 3, 'B')
            ->once()
            ->andReturn([
                (new Model\SpeechCongressmanProperties())
                        ->setSpeech(new Model\Speech())
                        ->setCongressman((
                            (new Model\CongressmanPartyProperties())
                                ->setAssembly((new Assembly())->setAssemblyId(1)->setFrom(new DateTime()))
                        )
                        ->setCongressman(new Model\Congressman()))
                ])
            ->getMock()
            ->shouldReceive('countByIssue')
            ->andReturn(100)
            ->getMock();

        $this->dispatch('/loggjafarthing/144/thingmal/b/3/raedur');

        $this->assertControllerName(\Althingi\Controller\SpeechController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $expectedData = (new Model\Speech())
            ->setSpeechId(4)
            ->setTo(new \DateTime('2000-01-01 00:01:00'))
            ->setFrom(new \DateTime('2000-01-01 00:00:00'))
            ->setPlenaryId(1)
            ->setAssemblyId(145)
            ->setIssueId(1)
            ->setCongressmanId(1);

        $this->getMockService(Service\Speech::class)
            ->shouldReceive('get')
            ->with(4)
            ->andReturn(
                (new Model\Speech())
                    ->setSpeechId(4)
                    ->setTo(new \DateTime('2000-01-01 00:00:01'))
                    ->setFrom(new \DateTime('2000-01-01 00:00:00'))
                    ->setPlenaryId(1)
                    ->setAssemblyId(145)
                    ->setIssueId(1)
                    ->setCongressmanId(1)
            )
            ->getMock()

            ->shouldReceive('update')
            ->with(Mockery::on(function ($actualData) use ($expectedData) {
                return $expectedData == $actualData;
            }))
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/144/thingmal/a/3/raedur/4', 'PATCH', [
            'to' => '2000-01-01 00:01:00',
        ]);

        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::patch
     */
    public function testPatchInvalid()
    {
        $this->getMockService(Service\Speech::class)
            ->shouldReceive('get')
            ->andReturn(new Model\Speech())
            ->getMock();

        $this->dispatch('/loggjafarthing/144/thingmal/a/3/raedur/4', 'PATCH', [
            'from' => 'invalid date',
        ]);

        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatchNotFound()
    {
        $this->getMockService(Service\Speech::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/144/thingmal/a/3/raedur/4', 'PATCH');

        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::options
     */
    public function testOptions()
    {
        $this->dispatch('/loggjafarthing/144/thingmal/a/3/raedur/4', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS', 'PUT', 'PATCH',];
        $allows = $this->getResponse()->getHeader('Allow');
        $actualMethods = array_map(function ($item) {
            return trim($item);
        }, explode(',', $allows[0]));

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }

    /**
     * @covers ::optionsList
     */
    public function testOptionsList()
    {
        $this->dispatch('/loggjafarthing/144/thingmal/a/3/raedur', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS'];
        $allows = $this->getResponse()->getHeader('Allow');
        $actualMethods = array_map(function ($item) {
            return trim($item);
        }, explode(',', $allows[0]));

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }
}
