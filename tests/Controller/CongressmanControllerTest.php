<?php

namespace Althingi\Controller;

use Althingi\Model\CongressmanAndParty;
use Althingi\Model\Session as SessionModel;
use Althingi\Model\Issue as IssueModel;
use Althingi\Model\IssueCategoryAndTime as IssueCategoryAndTimeModel;
use Althingi\Service\Congressman;
use Althingi\Service\Issue;
use Althingi\Service\IssueCategory;
use Althingi\Service\Party;
use Althingi\Service\Session;
use Althingi\Service\Speech;
use Althingi\Service\Vote;
use Althingi\Service\VoteItem;
use Althingi\Model\Congressman as CongressmanModel;
use Althingi\Model\Party as PartyModel;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class CongressmanControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CongressmanController
 * @covers \Althingi\Controller\CongressmanController::setCongressmanService
 * @covers \Althingi\Controller\CongressmanController::setPartyService
 * @covers \Althingi\Controller\CongressmanController::setSessionService
 * @covers \Althingi\Controller\CongressmanController::setVoteService
 * @covers \Althingi\Controller\CongressmanController::setIssueService
 * @covers \Althingi\Controller\CongressmanController::setSpeechService
 * @covers \Althingi\Controller\CongressmanController::setIssueCategoryService
 * @covers \Althingi\Controller\CongressmanController::setVoteItemService
 */
class CongressmanControllerTest extends AbstractHttpControllerTestCase
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
            Party::class,
            Session::class,
            Vote::class,
            VoteItem::class,
            Issue::class,
            Speech::class,
            IssueCategory::class

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
    public function testGet()
    {
        $this->getMockService(Congressman::class)
            ->shouldReceive('get')
            ->andReturn(
                (new CongressmanModel())
                ->setCongressmanId(1)
            )->once()
            ->getMock();

        $this->getMockService(Party::class)
            ->shouldReceive('fetchByCongressman')
            ->with(1)
            ->andReturn(
                [(new PartyModel())]
            )->once()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'GET');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetResourceNotFound()
    {
        $this->getMockService(Congressman::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(null)
            ->getMock();

        $this->getMockService(Party::class)
            ->shouldReceive('fetchByCongressman')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'GET');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Congressman::class)
            ->shouldReceive('count')
            ->once()
            ->andReturn(1)
            ->getMock()

            ->shouldReceive('fetchAll')
            ->once()
            ->andReturn([new CongressmanModel()])
            ->getMock();

        $this->dispatch('/thingmenn', 'GET');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('getList');
    }

    /**
     * @covers ::put
     */
    public function testPutSuccess()
    {
        $this->getMockService(Congressman::class)
            ->shouldReceive('create')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/thingmenn/1', 'PUT', [
            'name' => 'some name',
            'birth' => '1978-04-11'
        ]);

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::put
     */
    public function testPutInvalidData()
    {
        $this->getMockService(Congressman::class)
            ->shouldReceive('create')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'PUT', [
            'name' => 'some name',
            'birth' => 'not a date'
        ]);

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $this->getMockService(Congressman::class)
            ->shouldReceive('get')
            ->andReturn(
                (new CongressmanModel())
                    ->setCongressmanId(1)
                    ->setBirth(new \DateTime('1978-04-11'))
            )->once()
            ->getMock()

            ->shouldReceive('update')
            ->once()
            ->andReturn(1)
            ->getMock();


        $this->dispatch('/thingmenn/1', 'PATCH', [
            'name' => 'some name',
        ]);

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::patch
     */
    public function testPatchInvalidData()
    {
        $this->getMockService(Congressman::class)
            ->shouldReceive('get')
            ->andReturn(
                (new CongressmanModel())
                    ->setCongressmanId(1)
                    ->setName('My Namesson')
                    ->setBirth(new \DateTime('1978-04-11'))
            )->once()
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->getMock();


        $this->dispatch('/thingmenn/1', 'PATCH', [
            'birth' => 'invalid date',
        ]);

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatchResourceNotFound()
    {
        $this->getMockService(Congressman::class)
            ->shouldReceive('get')
            ->andReturn(null)->once()
            ->getMock()
            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'PATCH', [
            'birth' => '1978-04-11',
        ]);

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::delete
     */
    public function testDeleteSuccess()
    {
        $this->getMockService(Congressman::class)
            ->shouldReceive('get')
            ->andReturn(
                (new CongressmanModel())
                    ->setCongressmanId(1)
                    ->setName('My Namesson')
                    ->setBirth(new \DateTime('1978-04-11'))
            )->once()
            ->getMock()

            ->shouldReceive('delete')
            ->once()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'DELETE');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('delete');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::delete
     */
    public function testDeleteResourceNotFound()
    {
        $this->getMockService(Congressman::class)
            ->shouldReceive('get')
            ->andReturn(null)->once()
            ->getMock()

            ->shouldReceive('delete')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'DELETE');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('delete');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::options
     */
    public function testOptions()
    {
        $this->dispatch('/thingmenn/1', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS', 'PUT', 'PATCH', 'DELETE'];
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
        $this->dispatch('/thingmenn', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS'];
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }

    /**
     * @covers ::assemblyAction
     */
    public function testAssemblyAction()
    {
        $this->getMockService(Congressman::class)
            ->shouldReceive('fetchByAssembly')
            ->with(1, null)
            ->once()
            ->andReturn([
                (new CongressmanAndParty())->setPartyId(100)
            ])
            ->getMock();

        $this->getMockService(Party::class)
            ->shouldReceive('get')
            ->with(100)
            ->andReturn(new PartyModel())
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('assembly');
        $this->assertResponseStatusCode(200);

//        print_r(json_decode($this->getResponse()->getContent()));
    }

    /**
     * @covers ::assemblySpeechTimeAction
     */
    public function testAssemblySpeechTimeAction()
    {
        $this->getMockService(Speech::class)
            ->shouldReceive('getFrequencyByAssemblyAndCongressman')
            ->with(1, 2)
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/2/raedutimar');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('assembly-speech-time');
        $this->assertResponseStatusCode(200);

//        print_r(json_decode($this->getResponse()->getContent()));
    }

    /**
     * @covers ::assemblySessionsAction
     */
    public function testAssemblySessionsAction()
    {
        $this->getMockService(Session::class)
            ->shouldReceive('fetchByAssemblyAndCongressman')
            ->with(1, 2)
            ->once()
            ->andReturn([
                (new SessionModel())
            ])
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/2/thingseta');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('assembly-sessions');
        $this->assertResponseStatusCode(200);

//        print_r(json_decode($this->getResponse()->getContent()));
    }

    /**
     * @covers ::assemblyIssuesAction
     */
    public function testAssemblyIssuesAction()
    {
        $this->getMockService(Issue::class)
            ->shouldReceive('fetchByAssemblyAndCongressman')
            ->with(1, 2)
            ->once()
            ->andReturn([new IssueModel()])
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/2/thingmal');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('assembly-issues');
        $this->assertResponseStatusCode(200);

//        print_r(json_decode($this->getResponse()->getContent()));
    }

    /**
     * @covers ::assemblyVotingAction
     */
    public function testAssemblyVotingAction()
    {
        $this->getMockService(Vote::class)
            ->shouldReceive('getFrequencyByAssemblyAndCongressman')
            ->with(1, 2, null, null)
            ->once()
            ->andReturn([])
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/2/atvaedagreidslur');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('assembly-voting');
        $this->assertResponseStatusCode(200);

//        print_r(json_decode($this->getResponse()->getContent()));
    }

    /**
     * @covers ::assemblyCategoriesAction
     */
    public function testAssemblyCategoriesAction()
    {
        $this->getMockService(IssueCategory::class)
            ->shouldReceive('fetchFrequencyByAssemblyAndCongressman')
            ->with(1, 2)
            ->once()
            ->andReturn([new IssueCategoryAndTimeModel()])
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/2/malaflokkar');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('assembly-categories');
        $this->assertResponseStatusCode(200);

//        print_r(json_decode($this->getResponse()->getContent()));
    }

    /**
     * @covers ::assemblyVoteCategoriesAction
     */
    public function testAssemblyVoteCategoriesAction()
    {
        $this->getMockService(VoteItem::class)
            ->shouldReceive('fetchVoteByAssemblyAndCongressmanAndCategory')
            ->with(1, 2)
            ->once()
            ->andReturn([])
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/2/atvaedagreidslur-malaflokkar');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('assembly-vote-categories');
        $this->assertResponseStatusCode(200);

//        print_r(json_decode($this->getResponse()->getContent()));
    }
}
