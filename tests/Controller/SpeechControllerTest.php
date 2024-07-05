<?php

namespace Althingi\Controller;

use Althingi\{Model, Service};
use Althingi\Controller\SpeechController;
use Althingi\ServiceHelper;
use DateTime;
use Library\Container\Container;
use Mockery;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(SpeechController::class)]
#[CoversMethod(SpeechController::class, 'setCongressmanService')]
#[CoversMethod(SpeechController::class, 'setPartyService')]
#[CoversMethod(SpeechController::class, 'setSpeechService')]
#[CoversMethod(SpeechController::class, 'setParliamentarySessionService')]
#[CoversMethod(SpeechController::class, 'setConstituencyService')]
#[CoversMethod(SpeechController::class, 'get')]
#[CoversMethod(SpeechController::class, 'getList')]
#[CoversMethod(SpeechController::class, 'options')]
#[CoversMethod(SpeechController::class, 'optionsList')]
#[CoversMethod(SpeechController::class, 'patch')]
#[CoversMethod(SpeechController::class, 'put')]
class SpeechControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Speech::class,
            Service\Congressman::class,
            Service\Party::class,
            Service\ParliamentarySession::class,
            Service\Constituency::class,
        ]);
    }

    #[After]
    public function down(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function getSuccess()
    {
        $this->getMockService(Service\Speech::class)
            ->shouldReceive('countByIssue')
            ->once()
            ->andReturn(100)
            ->getMock()

            ->shouldReceive('fetch')
            ->with(4, 1, 3, 25, Model\KindEnum::A)
            ->andReturn([
                (new Model\SpeechAndPosition())
                    ->setKind(Model\KindEnum::A)
                    ->setPosition(1)
                    ->setCongressmanId(1)
                    ->setFrom(new \DateTime())

            ])
            ->once()
            ->getMock();

        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->andReturn(
                (new Model\Congressman())
                    ->setCongressmanId(1)
            )
            ->getMock();

        $this->getMockService(Service\Party::class)
            ->shouldReceive('getByCongressman')
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

        $this->dispatch('/loggjafarthing/1/thingmal/a/3/raedur/4', 'GET');

        $this->assertControllerName(SpeechController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function getRangeHeaders()
    {
        $this->getMockService(Service\Speech::class)
            ->shouldReceive('countByIssue')
            ->andReturn(100)
            ->getMock()
            ->shouldReceive('fetch')
            ->andReturn(array_map(function ($i) {
                return  (new Model\SpeechAndPosition())
                    ->setKind(Model\KindEnum::A)
                    ->setCongressmanId(1)
                    ->setText('<?xml version="1.0" ?><root />')
                    ->setFrom(new \DateTime('2000-01-01'))
                    ->setPosition($i);
            }, range(25, 49)))
            ->getMock();

        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->andReturn(new Model\Congressman())
            ->getMock();

        $this->getMockService(Service\Party::class)
            ->shouldReceive('getByCongressman')
            ->andReturn(
                (new Model\Party())
                    ->setPartyId(1)
                    ->setName('name')
            )
            ->getMock();

        $this->getMockService(Service\Constituency::class)
            ->shouldReceive('getByCongressman')
            ->times(25)
            ->andReturn(
                (new Model\ConstituencyDate())
                    ->setConstituencyId(1)
            )
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/3/raedur/4', 'GET');
        $resp = $this->getResponse();
        /** @var  $contentRange \Laminas\Http\Header\ContentRange */
        $contentRange = $this->getResponse()
            ->getHeader('Content-Range');

        $this->assertEquals('items 25-49/100', $contentRange[0]);
    }

    #[Test]
    public function putSuccess()
    {
        $expectedData = (new Model\Speech())
            ->setParliamentarySessionId(20)
            ->setCongressmanId(10)
            ->setIteration('*')
            ->setAssemblyId(1)
            ->setIssueId(3)
            ->setSpeechId('20210613T012100')
            ->setFrom(new \DateTime('2001-01-01 00:00:00'))
            ->setTo(new \DateTime('2001-01-01 00:00:00'))
            ->setType('t1')
            ->setText('t2')
            ->setKind(Model\KindEnum::A)
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
            'parliamentary_session_id' => 20,
            'assembly_id' => 1,
            'issue_id' => 3,
            'congressman_id' => 10,
            'congressman_type' => null,
            'iteration' => '*',
            'type' => 't1',
            'text' => 't2',
            'kind' => 'a',
            'validated' => 'false',
        ]);

        $this->assertControllerName(SpeechController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function putInvalidForm()
    {
        $this->getMockService(Service\Speech::class)
            ->shouldReceive('create')
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/3/raedur/4', 'PUT', [
            'parliamentary_session_id' => 20,
            'congressman_id' => 10,
            'congressman_type' => null,
            'iteration' => '*',
            'type' => 't1',
            'text' => 't2'
        ]);

        $this->assertControllerName(SpeechController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    #[Test]
    public function putDuplicate()
    {
        $exception = new \PDOException();
        $exception->errorInfo = ['', 1452, ''];

        $this->getMockService(Service\Speech::class)
            ->shouldReceive('save')
            ->andThrow($exception)
            ->twice()
            ->getMock();

        $this->getMockService(Service\ParliamentarySession::class)
            ->shouldReceive('save')
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/3/raedur/4', 'PUT', [
            'from' => '2001-01-01 00:00:00',
            'to' => '2001-01-01 00:00:00',
            'parliamentary_session_id' => 20,
            'congressman_id' => 10,
            'congressman_type' => null,
            'iteration' => '*',
            'type' => 't1',
            'text' => 't2',
            'validated' => 'false'
        ]);

        $this->assertControllerName(SpeechController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(500);
    }

    #[Test]
    public function putSomeError()
    {
        $exception = new \PDOException();
        $exception->errorInfo = ['', 0, ''];

        $this->getMockService(Service\Speech::class)
            ->shouldReceive('save')
            ->andThrow($exception)
            ->once()
            ->getMock();

        $this->getMockService(Service\ParliamentarySession::class)
            ->shouldReceive('save')
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/3/raedur/4', 'PUT', [
            'from' => '2001-01-01 00:00:00',
            'to' => '2001-01-01 00:00:00',
            'parliamentary_session_id' => 20,
            'congressman_id' => 10,
            'congressman_type' => null,
            'iteration' => '*',
            'type' => 't1',
            'text' => 't2',
            'validated' => 'false'
        ]);

        $this->assertControllerName(SpeechController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(500);
    }

    #[Test]
    public function getList()
    {
        $this->getMockService(Service\Speech::class)
            ->shouldReceive('fetchAllByIssue')
            ->with(144, 3, Model\KindEnum::B)
            ->once()
            ->andReturn([
                (new Model\SpeechCongressmanProperties())
                        ->setSpeech((new Model\Speech())->setKind(Model\KindEnum::A))
                        ->setCongressman((
                            (new Model\CongressmanPartyProperties())
                                ->setAssembly((new Model\Assembly())->setAssemblyId(1)->setFrom(new DateTime()))
                        )
                        ->setCongressman(new Model\Congressman()))
                ])
            ->getMock()
            ->shouldReceive('countByIssue')
            ->andReturn(100)
            ->getMock();

        $this->dispatch('/loggjafarthing/144/thingmal/b/3/raedur');

        $this->assertControllerName(SpeechController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function patchSuccess()
    {
        $expectedData = (new Model\Speech())
            ->setSpeechId(4)
            ->setTo(new \DateTime('2000-01-01 00:01:00'))
            ->setFrom(new \DateTime('2000-01-01 00:00:00'))
            ->setParliamentarySessionId(1)
            ->setAssemblyId(145)
            ->setIssueId(1)
            ->setCongressmanId(1)
            ->setKind(Model\KindEnum::A)
        ;

        $this->getMockService(Service\Speech::class)
            ->shouldReceive('get')
            ->with(4)
            ->andReturn(
                (new Model\Speech())
                    ->setSpeechId(4)
                    ->setTo(new \DateTime('2000-01-01 00:00:01'))
                    ->setFrom(new \DateTime('2000-01-01 00:00:00'))
                    ->setParliamentarySessionId(1)
                    ->setAssemblyId(145)
                    ->setIssueId(1)
                    ->setCongressmanId(1)
                    ->setKind(Model\KindEnum::A)
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

    #[Test]
    public function patchInvalid()
    {
        $this->getMockService(Service\Speech::class)
            ->shouldReceive('get')
            ->andReturn((new Model\Speech())->setKind(Model\KindEnum::A))
            ->getMock();

        $this->dispatch('/loggjafarthing/144/thingmal/a/3/raedur/4', 'PATCH', [
            'from' => 'invalid date',
        ]);

        $this->assertResponseStatusCode(400);
    }

    #[Test]
    public function patchNotFound()
    {
        $this->getMockService(Service\Speech::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/144/thingmal/a/3/raedur/4', 'PATCH');

        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function optionsSuccessful()
    {
        $this->dispatch('/loggjafarthing/144/thingmal/a/3/raedur/4', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS', 'PUT', 'PATCH',];
        $allows = $this->getResponse()->getHeader('Allow');
        $actualMethods = array_map(function ($item) {
            return trim($item);
        }, explode(',', $allows[0]));

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }

    #[Test]
    public function optionsList()
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
