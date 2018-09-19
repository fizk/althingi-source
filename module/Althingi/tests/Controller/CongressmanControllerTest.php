<?php

namespace AlthingiTest\Controller;

use Althingi\Model\CongressmanAndParty;
use Althingi\Model\Session as SessionModel;
use Althingi\Model\Issue as IssueModel;
use Althingi\Model\IssueCategoryAndTime as IssueCategoryAndTimeModel;
use Althingi\Service\Assembly;
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
use AlthingiTest\ServiceHelper;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\Stdlib\ArrayUtils;

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
 * @covers \Althingi\Controller\CongressmanController::setAssemblyService
 */
class CongressmanControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        // The module configuration should still be applicable for tests.
        // You can override configuration here with test case specific values,
        // such as sample view templates, path stacks, module_listener_options,
        // etc.
        $configOverrides = [];
        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));

        parent::setUp();

        $this->buildServices([
            Congressman::class,
            Party::class,
            Session::class,
            Vote::class,
            VoteItem::class,
            Issue::class,
            Speech::class,
            IssueCategory::class,
            Assembly::class,
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
        $this->assertResponseStatusCode(206);
        $this->assertResponseHeaderContains('Content-Range', 'items 0-1/1');
        $this->assertResponseHeaderContains('Range-Unit', 'items');
    }

    /**
     * @covers ::put
     */
    public function testPutSuccess()
    {
        $this->getMockService(Congressman::class)
            ->shouldReceive('save')
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
            'birth' => '1978-04-11'
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
        $this->getMockService(Assembly::class)
            ->shouldReceive('get')
            ->with(1)
            ->once()
            ->andReturn((new \Althingi\Model\Assembly())->setAssemblyId(1))
            ->getMock();

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
        $this->assertResponseStatusCode(206);
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
        $this->assertResponseStatusCode(206);

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
        $this->assertResponseStatusCode(206);

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
        $this->assertResponseStatusCode(206);

//        print_r(json_decode($this->getResponse()->getContent()));
    }

    /**
     * @covers ::assemblyCategoriesAction
     */
    public function testAssemblyCategoriesAction()
    {
        $this->getMockService(IssueCategory::class)
            ->shouldReceive('fetchFrequencyByAssemblyAndCongressman')
            ->with(1, 2, ['A'])
            ->once()
            ->andReturn([new IssueCategoryAndTimeModel()])
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/2/malaflokkar');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('assembly-categories');
        $this->assertResponseStatusCode(206);

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
        $this->assertResponseStatusCode(206);

//        print_r(json_decode($this->getResponse()->getContent()));
    }

    /**
     * @covers ::assemblyTimesAction
     */
    public function testAssemblyTimesAction()
    {
        $this->getMockService(Assembly::class)
            ->shouldReceive('get')
            ->with(1)
            ->andReturn((new \Althingi\Model\Assembly())->setAssemblyId(1)->setFrom(new \DateTime()))
            ->once()
            ->getMock();

        $this->getMockService(Congressman::class)
            ->shouldReceive('fetchTimeByAssembly')
            ->with(1, null, 'desc', ['A'])
            ->once()
            ->andReturn([
                (new \Althingi\Model\Congressman())->setCongressmanId(1)
            ]);

        $this->getMockService(Party::class)
            ->shouldReceive('getByCongressman')
            ->andReturn((new \Althingi\Model\Party()))
            ->once();

        $this->dispatch('/loggjafarthing/1/thingmenn/raedutimar');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('assembly-times');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::assemblyQuestionsAction
     */
    public function testAssemblyQuestionsAction()
    {
        $this->getMockService(Assembly::class)
            ->shouldReceive('get')
            ->with(1)
            ->andReturn((new \Althingi\Model\Assembly())->setAssemblyId(1)->setFrom(new \DateTime()))
            ->once()
            ->getMock();

        $this->getMockService(Congressman::class)
            ->shouldReceive('fetchIssueTypeCountByAssembly')
            ->with(1, null, ['q', 'm'], 'desc')
            ->once()
            ->andReturn([
                (new \Althingi\Model\Congressman())->setCongressmanId(1)
            ]);

        $this->getMockService(Party::class)
            ->shouldReceive('getByCongressman')
            ->andReturn((new \Althingi\Model\Party()))
            ->once();

        $this->dispatch('/loggjafarthing/1/thingmenn/fyrirspurnir');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('assembly-questions');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::assemblyResolutionsAction
     */
    public function testAssemblyResolutionsAction()
    {
        $this->getMockService(Assembly::class)
            ->shouldReceive('get')
            ->with(1)
            ->andReturn((new \Althingi\Model\Assembly())->setAssemblyId(1)->setFrom(new \DateTime()))
            ->once()
            ->getMock();

        $this->getMockService(Congressman::class)
            ->shouldReceive('fetchIssueTypeCountByAssembly')
            ->with(1, null, ['a'], 'desc')
            ->once()
            ->andReturn([
                (new \Althingi\Model\Congressman())->setCongressmanId(1)
            ]);

        $this->getMockService(Party::class)
            ->shouldReceive('getByCongressman')
            ->andReturn((new \Althingi\Model\Party()))
            ->once();

        $this->dispatch('/loggjafarthing/1/thingmenn/thingsalyktanir');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('assembly-resolutions');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::assemblyBillsAction
     */
    public function testAssemblyBillsAction()
    {
        $this->getMockService(Assembly::class)
            ->shouldReceive('get')
            ->with(1)
            ->andReturn((new \Althingi\Model\Assembly())->setAssemblyId(1)->setFrom(new \DateTime()))
            ->once()
            ->getMock();

        $this->getMockService(Congressman::class)
            ->shouldReceive('fetchIssueTypeCountByAssembly')
            ->with(1, null, ['l'], 'desc')
            ->once()
            ->andReturn([
                (new \Althingi\Model\Congressman())->setCongressmanId(1)
            ]);

        $this->getMockService(Party::class)
            ->shouldReceive('getByCongressman')
            ->andReturn((new \Althingi\Model\Party()))
            ->once();

        $this->dispatch('/loggjafarthing/1/thingmenn/lagafrumvorp');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('assembly-bills');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::assemblyIssuesSummaryAction
     */
    public function testAssemblyIssuesSummaryAction()
    {
        $this->getMockService(Issue::class)
            ->shouldReceive('fetchByAssemblyAndCongressmanSummary')
            ->with(1, 2)
            ->andReturn([
                (new \Althingi\Model\Issue())
            ])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/2/thingmal-samantekt');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('assembly-issues-summary');
        $this->assertResponseStatusCode(206);
    }
}
