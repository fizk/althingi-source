<?php

namespace Althingi\Controller;

use Althingi\Model\Proponent;
use Althingi\Service\Assembly;
use Althingi\Service\Congressman;
use Althingi\Service\Document;
use Althingi\Service\Issue;
use Althingi\Service\Party;
use Althingi\Service\Speech;
use Althingi\Service\Vote;
use Althingi\Model\CongressmanAndDateRange;
use Althingi\Model\Issue as IssueModel;
use Althingi\Model\Party as PartyModel;
use Althingi\Model\Assembly as AssemblyModel;
use Althingi\Model\IssueAndDate as IssueAndDateModel;
use Althingi\Model\IssueValue as IssueValueModel;
use Mockery;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class IssueControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\IssueController
 * @covers \Althingi\Controller\IssueController::setCongressmanService
 * @covers \Althingi\Controller\IssueController::setIssueService
 * @covers \Althingi\Controller\IssueController::setPartyService
 * @covers \Althingi\Controller\IssueController::setDocumentService
 * @covers \Althingi\Controller\IssueController::setVoteService
 * @covers \Althingi\Controller\IssueController::setAssemblyService
 * @covers \Althingi\Controller\IssueController::setSpeechService
 */
class IssueControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Congressman::class,
            Issue::class,
            Party::class,
            Document::class,
            Vote::class,
            Assembly::class,
            Speech::class,
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
        $this->getMockService(Issue::class)
            ->shouldReceive('getWithDate')
            ->andReturn((new IssueAndDateModel())->setCongressmanId(1)->setDate(new \DateTime()))
            ->once()
            ->getMock();

        $this->getMockService(Assembly::class)
            ->shouldReceive('get')
            ->andReturn((new AssemblyModel)->setFrom(new \DateTime('2001-02-01')))
            ->once()
            ->getMock();

        $this->getMockService(Congressman::class)
            ->shouldReceive('fetchProponentsByIssue')
            ->andReturn([(new Proponent())->setCongressmanId(1)])
            ->once()
            ->getMock()
            ->shouldReceive('fetchAccumulatedTimeByIssue')
            ->andReturn([(new CongressmanAndDateRange())->setBegin(new \DateTime())->setCongressmanId(1)])
            ->once()
            ->getMock();

        $this->getMockService(Party::class)
            ->shouldReceive('getByCongressman')
            ->andReturn((new PartyModel()))
            ->twice()
            ->getMock();

        $this->getMockService(Vote::class)
            ->shouldReceive('fetchDateFrequencyByIssue')
            ->andReturn([])
            ->once()
            ->getMock();

        $this->getMockService(Speech::class)
            ->shouldReceive('fetchFrequencyByIssue')
            ->andReturn([])
            ->once()
            ->getMock();


        $this->dispatch('/loggjafarthing/100/thingmal/200', 'GET');

        $this->assertControllerClass('IssueController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::speechTimesAction
     */
    public function testGetSpeechTime()
    {
        $this->getMockService(Assembly::class)
            ->shouldReceive('get')
            ->andReturn((new \Althingi\Model\Assembly())->setAssemblyId(1)->setFrom(new \DateTime()))
            ->once()
            ->getMock()
        ;

        $this->getMockService(Issue::class)
            ->shouldReceive('fetchByAssemblyAndSpeechTime')
            ->andReturn(array_map(function ($i) {
                return (new IssueValueModel())->setCongressmanId(1)->setIssueId($i)->setValue($i);
            }, range(0, 24)))
            ->once()
            ->getMock()
        ;

        $this->getMockService(Party::class)
            ->shouldReceive('getByCongressman')
            ->andReturn(null)
            ->times(25)
            ->getMock()
        ;

        $this->getMockService(Congressman::class)
            ->shouldReceive('fetchProponentsByIssue')
            ->andReturn([(new Proponent())->setCongressmanId(1)])
            ->times(25)
            ->getMock()
        ;

        $this->dispatch('/loggjafarthing/100/thingmal/raedutimar', 'GET');

        $this->assertControllerClass('IssueController');
        $this->assertActionName('speech-times');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Issue::class)
            ->shouldReceive('getWithDate')
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->getMockService(Assembly::class)
            ->shouldReceive('get')
            ->never()
            ->getMock();

        $this->getMockService(Congressman::class)
            ->shouldReceive('get')
            ->never()
            ->getMock()
            ->shouldReceive('fetchAccumulatedTimeByIssue')
            ->never()
            ->getMock();

        $this->getMockService(Party::class)
            ->shouldReceive('getByCongressman')
            ->never()
            ->getMock();

        $this->getMockService(Vote::class)
            ->shouldReceive('fetchDateFrequencyByIssue')
            ->never()
            ->getMock();

        $this->getMockService(Speech::class)
            ->shouldReceive('fetchFrequencyByIssue')
            ->never()
            ->getMock();


        $this->dispatch('/loggjafarthing/100/thingmal/200', 'GET');

        $this->assertControllerClass('IssueController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Issue::class)
            ->shouldReceive('countByAssembly')
            ->andReturn(123)
            ->once()
            ->getMock()
            ->shouldReceive('fetchByAssembly')
            ->andReturn(array_map(function () {
                    return (new IssueAndDateModel())->setDate(new \DateTime())->setCongressmanId(1)->setIssueId(1);
            }, range(0, 24)))
            ->once()
            ->getMock()
        ;

        $this->getMockService(Party::class)
            ->shouldReceive('getByCongressman')
            ->andReturn(null)
            ->times(25)
            ->getMock()
        ;

        $this->getMockService(Congressman::class)
            ->shouldReceive('fetchProponentsByIssue')
            ->andReturn([(new Proponent())->setCongressmanId(1)])
            ->times(25)
            ->getMock()
        ;

        $this->dispatch('/loggjafarthing/100/thingmal', 'GET');
        $this->assertControllerClass('IssueController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
        $this->assertResponseHeaderContains('Range-Unit', 'items');
        $this->assertResponseHeaderContains('Content-Range', 'items 0-25/123');
    }

    /**
     * @covers ::put
     */
    public function testPutSuccess()
    {
        $expectedObject = (new IssueModel())
            ->setIssueId(200)
            ->setAssemblyId(100)
            ->setName('n1')
            ->setCategory('c1')
            ->setType('1')
            ->setTypeName('tn')
            ->setTypeSubname('tsn')
        ;

        $this->getMockService(Issue::class)
            ->shouldReceive('save')
            ->with(Mockery::on(function ($actualObject) use ($expectedObject) {
                return $expectedObject == $actualObject;
            }))
            ->andReturn(1)
            ->once()
            ->getMock()
        ;

        $this->dispatch('/loggjafarthing/100/thingmal/200', 'PUT', [
            'name' => 'n1',
            'category' => 'c1',
            'type' => '1',
            'type_name' => 'tn',
            'type_subname' => 'tsn',
        ]);

        $this->assertControllerClass('IssueController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::put
     */
    public function testPutInvalidForm()
    {
        $this->getMockService(Issue::class)
            ->shouldReceive('create')
            ->never()
            ->getMock()
        ;

        $this->dispatch('/loggjafarthing/100/thingmal/200', 'PUT', [
            'type_name' => 'tn',
            'type_subname' => 'tsn',
        ]);

        $this->assertControllerClass('IssueController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatch()
    {
        $expectedObject = (new IssueModel())
            ->setIssueId(200)
            ->setAssemblyId(100)
            ->setName('n1')
            ->setCategory('c1')
            ->setType('1')
            ->setTypeName('tn')
            ->setTypeSubname('tsn')
        ;

        $this->getMockService(Issue::class)
            ->shouldReceive('get')
            ->once()
            ->with(200, 100)
            ->andReturn((new IssueModel())->setIssueId(200)->setAssemblyId(100))
            ->getMock()

            ->shouldReceive('update')
            ->with(Mockery::on(function ($actualObject) use ($expectedObject) {
                return $expectedObject == $actualObject;
            }))
            ->andReturn(1)
            ->once()
            ->getMock()
        ;

        $this->dispatch('/loggjafarthing/100/thingmal/200', 'PATCH', [
            'name' => 'n1',
            'category' => 'c1',
            'type' => '1',
            'type_name' => 'tn',
            'type_subname' => 'tsn',
        ]);

        $this->assertControllerClass('IssueController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::optionsList
     */
    public function testOptionsList()
    {
        $this->dispatch('/loggjafarthing/100/thingmal', 'OPTIONS');

        $this->assertControllerClass('IssueController');
        $this->assertActionName('optionslist');
        $this->assertResponseStatusCode(200);

        $expectedMethods = ['GET', 'OPTIONS'];
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }

    /**
     * @covers ::options
     */
    public function testOptions()
    {
        $this->dispatch('/loggjafarthing/100/thingmal/200', 'OPTIONS');

        $this->assertControllerClass('IssueController');
        $this->assertActionName('options');
        $this->assertResponseStatusCode(200);

        $expectedMethods = ['GET', 'OPTIONS', 'PUT', 'PATCH'];
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }
}
