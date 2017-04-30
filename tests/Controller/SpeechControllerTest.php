<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Althingi\Model\SpeechAndPosition;
use Althingi\Service\Congressman;
use Althingi\Service\Party;
use Althingi\Service\Speech;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Mockery;

/**
 * Class SpeechControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\SpeechController
 * @covers \Althingi\Controller\SpeechController::setCongressmanService
 * @covers \Althingi\Controller\SpeechController::setPartyService
 * @covers \Althingi\Controller\SpeechController::setSpeechService
 */
class SpeechControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Speech::class,
            Congressman::class,
            Party::class,
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
        $this->getMockService(Speech::class)
            ->shouldReceive('countByIssue')
            ->andReturn(100)
            ->once()
            ->getMock()

            ->shouldReceive('fetch')
            ->with(4, 1, 3)
            ->andReturn([
                (new SpeechAndPosition())->setPosition(1)->setCongressmanId(1)->setFrom(new \DateTime())
            ])
            ->once()
            ->getMock();

        $this->getMockService(Congressman::class)
            ->shouldReceive('get')
            ->andReturn((new \Althingi\Model\Congressman())->setCongressmanId(1))
            ->getMock();
        $this->getMockService(Party::class)
            ->shouldReceive('getByCongressman')
            ->andReturn(new \Althingi\Model\Party())
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/3/raedur/4', 'GET');

        $this->assertControllerClass('SpeechController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::get
     */
    public function testGetRangeHeaders()
    {
        $this->getMockService(Speech::class)
            ->shouldReceive('countByIssue')
            ->andReturn(100)
            ->getMock()
            ->shouldReceive('fetch')
            ->andReturn(array_map(function ($i) {
                return  (new SpeechAndPosition())
                    ->setCongressmanId(1)
                    ->setText('<?xml version="1.0" ?><root />')
                    ->setFrom(new \DateTime('2000-01-01'))
                    ->setPosition($i);
            }, range(25, 49)))
            ->getMock();

        $this->getMockService(Congressman::class)
            ->shouldReceive('get')
            ->andReturn(new \Althingi\Model\Congressman())
            ->getMock();

        $this->getMockService(Party::class)
            ->shouldReceive('getByCongressman')
            ->andReturn(new \Althingi\Model\Party())
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/3/raedur/4', 'GET');

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
        $expectedData = (new \Althingi\Model\Speech())
            ->setPlenaryId(20)
            ->setCongressmanId(10)
            ->setIteration('*')
            ->setAssemblyId(1)
            ->setIssueId(3)
            ->setSpeechId(4)
            ->setFrom(new \DateTime('2001-01-01 00:00:00'))
            ->setTo(new \DateTime('2001-01-01 00:00:00'))
            ->setType('t1')
            ->setText('t2');

        $this->getMockService(Speech::class)
            ->shouldReceive('create')
            ->with(Mockery::on(function ($actualData) use ($expectedData) {
                return $actualData == $expectedData;
            }))
            ->andReturn(10)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/3/raedur/4', 'PUT', [
            'from' => '2001-01-01 00:00:00',
            'to' => '2001-01-01 00:00:00',
            'plenary_id' => 20,
            'congressman_id' => 10,
            'congressman_type' => null,
            'iteration' => '*',
            'type' => 't1',
            'text' => 't2'
        ]);

        $this->assertControllerClass('SpeechController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::put
     */
    public function testPutSuccessTeapot()
    {
        $this->getMockService(Speech::class)
            ->shouldReceive('create')
            ->andThrow(new \PDOException('some fk_Speach_Plenary1 some', 23000))
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/3/raedur/4', 'PUT', [
            'from' => '2001-01-01 00:00:00',
            'to' => '2001-01-01 00:00:00',
            'plenary_id' => 20,
            'congressman_id' => 10,
            'congressman_type' => null,
            'iteration' => '*',
            'type' => 't1',
            'text' => 't2'
        ]);

        $this->assertControllerClass('SpeechController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(418);
    }

    /**
     * @covers ::put
     */
    public function testPutInvalidForm()
    {
        $this->getMockService(Speech::class)
            ->shouldReceive('create')
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/3/raedur/4', 'PUT', [
            'plenary_id' => 20,
            'congressman_id' => 10,
            'congressman_type' => null,
            'iteration' => '*',
            'type' => 't1',
            'text' => 't2'
        ]);

        $this->assertControllerClass('SpeechController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Speech::class)
            ->shouldReceive('fetchByIssue')
            ->with(144, 3, 0, 25)
            ->andReturn([
                (new SpeechAndPosition())->setCongressmanId(1)->setFrom(new \DateTime())
            ])
            ->once()
            ->getMock()

            ->shouldReceive('countByIssue')
            ->andReturn(100)
            ->getMock();

        $this->getMockService(Congressman::class)
            ->shouldReceive('get')
            ->andReturn(new \Althingi\Model\Congressman())
            ->once();

        $this->getMockService(Party::class)
            ->shouldReceive('getByCongressman')
            ->andReturn(new \Althingi\Model\Party())
            ->once();

        $this->dispatch('/loggjafarthing/144/thingmal/3/raedur');

        $this->assertControllerClass('SpeechController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $expectedData = (new \Althingi\Model\Speech())
            ->setSpeechId(4)
            ->setTo(new \DateTime('2000-01-01 00:01:00'))
            ->setFrom(new \DateTime('2000-01-01 00:00:00'))
            ->setPlenaryId(1)
            ->setAssemblyId(145)
            ->setIssueId(1)
            ->setCongressmanId(1);

        $this->getMockService(Speech::class)
            ->shouldReceive('get')
            ->with(4)
            ->andReturn(
                (new \Althingi\Model\Speech())
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

        $this->dispatch('/loggjafarthing/144/thingmal/3/raedur/4', 'PATCH', [
            'to' => '2000-01-01 00:01:00',
        ]);

        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::patch
     */
    public function testPatchInvalid()
    {
        $this->getMockService(Speech::class)
            ->shouldReceive('get')
            ->andReturn(new \Althingi\Model\Speech())
            ->getMock();

        $this->dispatch('/loggjafarthing/144/thingmal/3/raedur/4', 'PATCH', [
            'from' => 'invalid date',
        ]);

        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatchNotFound()
    {
        $this->getMockService(Speech::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/144/thingmal/3/raedur/4', 'PATCH');

        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::options
     */
    public function testOptions()
    {
        $this->dispatch('/loggjafarthing/144/thingmal/3/raedur/4', 'OPTIONS');

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
        $this->dispatch('/loggjafarthing/144/thingmal/3/raedur', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS'];
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }
}
