<?php

namespace AlthingiTest\Controller;

use Althingi\Model;
use Althingi\Store;
use Althingi\Service;
use Althingi\Controller;
use AlthingiTest\ServiceHelper;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Mockery;

/**
 * Class SpeechControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\SpeechController
 * @covers \Althingi\Controller\SpeechController::setCongressmanService
 * @covers \Althingi\Controller\SpeechController::setPartyService
 * @covers \Althingi\Controller\SpeechController::setSpeechService
 * @covers \Althingi\Controller\SpeechController::setPlenaryService
 * @covers \Althingi\Controller\SpeechController::setConstituencyService
 */
class SpeechControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Service\Speech::class,
            Service\Congressman::class,
            Service\Party::class,
            Service\Plenary::class,
            Service\Constituency::class,
            Store\Speech::class
        ]);
    }

    public function tearDown()
    {
        \Mockery::close();
        return parent::tearDown();
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
                (new Model\SpeechAndPosition())->setPosition(1)->setCongressmanId(1)->setFrom(new \DateTime())
            ])
            ->once()
            ->getMock();

        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->andReturn((new \Althingi\Model\Congressman())->setCongressmanId(1))
            ->getMock();

        $this->getMockService(Service\Party::class)
            ->shouldReceive('getByCongressman')
            ->andReturn(new \Althingi\Model\Party())
            ->getMock();

        $this->getMockService(Service\Constituency::class)
            ->shouldReceive('getByCongressman')
            ->andReturn(new Model\ConstituencyDate())
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
            ->andReturn(new \Althingi\Model\Party())
            ->getMock();

        $this->getMockService(Service\Constituency::class)
            ->shouldReceive('getByCongressman')
            ->andReturn(new Model\ConstituencyDate())
            ->times(25)
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/3/raedur/4', 'GET');
        $resp = $this->getResponse();
        /** @var  $contentRange \Zend\Http\Header\ContentRange */
        $contentRange = $this->getResponse()
            ->getHeaders()
            ->get('Content-Range');

        $this->assertEquals('items 25-49/100', $contentRange->getFieldValue());
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
            ->setSpeechId(4)
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
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Store\Speech::class)
            ->shouldReceive('fetchByIssue')
            ->with(144, 3, 'B', 0, null, 1500)
            ->once()
            ->andReturn([
                (new Model\SpeechCongressmanProperties())
                        ->setSpeech(new Model\Speech())
                        ->setCongressman((new Model\CongressmanPartyProperties())
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
        $this->assertResponseHeaderContains('Content-Range', 'items 0-1/100');
        $this->assertResponseHeaderContains('Range-Unit', 'items');
    }

    /**
     * @covers ::getList
     */
//    public function testGetListRangeHeaders()
//    {
//        $headers = $this->getRequest()->getHeaders();
//        $headers->addHeaderLine('Range', '0-');
//
//        $this->getMockService(Store\Speech::class)
//            ->shouldReceive('fetchByIssue')
//            ->with(144, 3, 'B', 0, null, 1500)
//            ->once()
//            ->andReturn([
//                (new Model\SpeechCongressmanProperties())
//                    ->setSpeech(new Model\Speech())
//                    ->setCongressman((new Model\CongressmanPartyProperties())
//                        ->setCongressman(new Model\Congressman()))
//            ])
//            ->getMock()
//            ->shouldReceive('countByIssue')
//            ->andReturn(100)
//            ->getMock();
//
//        $this->dispatch('/loggjafarthing/144/thingmal/a/3/raedur');
//
//        /** @var  $contentRange \Zend\Http\Header\ContentRange */
//        $contentRange = $this->getResponse()
//            ->getHeaders()
//            ->get('Content-Range')
//        ;
//
//        $this->assertEquals('items 0-1/100', $contentRange->getFieldValue());
//    }

    /**
     * @covers ::getList
     */
//    public function testGetListRangeHeadersFixedRange()
//    {
//        $headers = $this->getRequest()->getHeaders();
//        $headers->addHeaderLine('Range', '0-20');
//
//        $this->getMockService(Speech::class)
//            ->shouldReceive('fetchByIssue')
//            ->with(144, 3, 'A', 0, 20, 1500)
//            ->andReturn(array_map(function ($i) {
//                return  (new SpeechAndPosition())
//                    ->setCongressmanId(1)
/*                    ->setText('<?xml version="1.0" ?><root />')*/
//                    ->setFrom(new \DateTime('2000-01-01'))
//                    ->setPosition($i);
//            }, range(0, 19)))
//            ->once()
//            ->getMock()
//
//            ->shouldReceive('countByIssue')
//            ->andReturn(100)
//            ->getMock();
//
//        $this->getMockService(Congressman::class)
//            ->shouldReceive('get')
//            ->andReturn(new \Althingi\Model\Congressman())
//            ->times(20);
//
//        $this->getMockService(Party::class)
//            ->shouldReceive('getByCongressman')
//            ->andReturn(new \Althingi\Model\Party())
//            ->times(20);
//
//        $this->getMockService(Constituency::class)
//            ->shouldReceive('getByCongressman')
//            ->andReturn(new ConstituencyDate())
//            ->times(20)
//            ->getMock();
//
//        $this->dispatch('/loggjafarthing/144/thingmal/a/3/raedur');
//
//        /** @var  $contentRange \Zend\Http\Header\ContentRange */
//        $contentRange = $this->getResponse()
//            ->getHeaders()
//            ->get('Content-Range');
//
//        $this->assertEquals('items 0-20/100', $contentRange->getFieldValue());
//    }

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
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }

    /**
     * @covers ::optionsList
     */
    public function testOptionsList()
    {
        $this->dispatch('/loggjafarthing/144/thingmal/a/3/raedur', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS'];
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }
}
