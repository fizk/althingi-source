<?php

namespace AlthingiTest\Controller;

use Althingi\Controller\CongressmanController;
use AlthingiTest\ServiceHelper;
use Althingi\Service;
use Althingi\Store;
use Althingi\Model;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\Stdlib\ArrayUtils;

/**
 * Class CongressmanControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CongressmanController
 * @covers \Althingi\Controller\CongressmanController::setCongressmanService
 * @covers \Althingi\Controller\CongressmanController::setPartyService
 * @covers \Althingi\Controller\CongressmanController::setVoteItemService
 * @covers \Althingi\Controller\CongressmanController::setAssemblyService
 * @covers \Althingi\Controller\CongressmanController::setCongressmanStore
 * @covers \Althingi\Controller\CongressmanController::setSessionStore
 * @covers \Althingi\Controller\CongressmanController::setVoteStore
 * @covers \Althingi\Controller\CongressmanController::setIssueStore
 */
class CongressmanControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $configOverrides = [];
        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));

        parent::setUp();

        $this->buildServices([
            Service\Congressman::class,
            Service\Party::class,
            Service\Session::class,
            Service\VoteItem::class,
            Service\Assembly::class,
            Store\Congressman::class,
            Store\Session::class,
            Store\Issue::class,
            Store\Vote::class,
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
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->andReturn(
                (new Model\Congressman())
                ->setCongressmanId(1)
            )->once()
            ->getMock();

        $this->getMockService(Service\Party::class)
            ->shouldReceive('fetchByCongressman')
            ->with(1)
            ->andReturn(
                [(new Model\Party())]
            )->once()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'GET');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetResourceNotFound()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(null)
            ->getMock();

        $this->getMockService(Service\Party::class)
            ->shouldReceive('fetchByCongressman')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'GET');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('count')
            ->once()
            ->andReturn(1)
            ->getMock()

            ->shouldReceive('fetchAll')
            ->once()
            ->andReturn([new Model\Congressman()])
            ->getMock();

        $this->dispatch('/thingmenn', 'GET');

        $this->assertControllerName(CongressmanController::class);
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
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/thingmenn/1', 'PUT', [
            'name' => 'some name',
            'birth' => '1978-04-11'
        ]);

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::put
     */
    public function testPutInvalidData()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('create')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'PUT', [
            'name' => 'some name',
            'birth' => 'not a date'
        ]);

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->andReturn(
                (new Model\Congressman())
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

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::patch
     */
    public function testPatchInvalidData()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->andReturn(
                (new Model\Congressman())
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

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatchResourceNotFound()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->andReturn(null)->once()
            ->getMock()
            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'PATCH', [
            'birth' => '1978-04-11',
        ]);

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::delete
     */
    public function testDeleteSuccess()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->andReturn(
                (new Model\Congressman())
                    ->setCongressmanId(1)
                    ->setName('My Namesson')
                    ->setBirth(new \DateTime('1978-04-11'))
            )->once()
            ->getMock()

            ->shouldReceive('delete')
            ->once()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'DELETE');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('delete');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::delete
     */
    public function testDeleteResourceNotFound()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->andReturn(null)->once()
            ->getMock()

            ->shouldReceive('delete')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'DELETE');

        $this->assertControllerName(CongressmanController::class);
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
        $this->getMockService(Store\Congressman::class)
            ->shouldReceive('fetchByAssembly')
            ->andReturn([])
            ->getMock();

        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('get')
            ->with(1)
            ->once()
            ->andReturn((new Model\Assembly())->setAssemblyId(1))
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::assemblyCongressmanAction
     */
    public function testAssemblyCongressmanAction()
    {
        $this->getMockService(Store\Congressman::class)
            ->shouldReceive('getByAssembly')
            ->with(1, 1)
            ->once()
            ->andReturn(
                (new Model\CongressmanPartyProperties())
                ->setParty(new Model\Party())
                ->setConstituency(new Model\Constituency())
                ->setCongressman(new Model\Congressman())
            )
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/1');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly-congressman');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::assemblyCongressmanAction
     */
    public function testAssemblyCongressmanActionNotFound()
    {
        $this->getMockService(Store\Congressman::class)
            ->shouldReceive('getByAssembly')
            ->with(1, 1)
            ->once()
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/1');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly-congressman');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::assemblyCongressmanOtherDocsAction
     */
    public function testAssemblyCongressmanOtherDocsAction()
    {
        $this->getMockService(Store\Congressman::class)
            ->shouldReceive('fetchOtherDocumentsByAssembly')
            ->with(1, 1)
            ->once()
            ->andReturn([])
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/1/onnur-skjol');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly-congressman-other-docs');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::assemblySessionsAction
     */
    public function testAssemblySessionsAction()
    {
        $this->getMockService(Store\Session::class)
            ->shouldReceive('fetchByAssemblyAndCongressman')
            ->with(1, 2)
            ->once()
            ->andReturn([
                (new Model\Session())
            ])
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/2/thingseta');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly-sessions');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::assemblyIssuesAction
     */
    public function testAssemblyIssuesAction()
    {
        $this->getMockService(Store\Issue::class)
            ->shouldReceive('fetchByAssemblyAndCongressman')
            ->with(1, 2)
            ->once()
            ->andReturn([new Model\Issue()])
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/2/thingmal');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly-issues');
        $this->assertResponseStatusCode(206);

//        print_r(json_decode($this->getResponse()->getContent()));
    }

    /**
     * @covers ::assemblyVotingAction
     */
    public function testAssemblyVotingAction()
    {
        $this->getMockService(Store\Vote::class)
            ->shouldReceive('getFrequencyByAssemblyAndCongressman')
            ->with(1, 2)
            ->once()
            ->andReturn([])
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/2/atvaedagreidslur');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly-voting');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::assemblyCategoriesAction
     */
    public function testAssemblyCategoriesAction()
    {
        $this->getMockService(Store\Issue::class)
            ->shouldReceive('fetchFrequencyByAssemblyAndCongressman')
            ->with(1, 2)
            ->once()
            ->andReturn([new Model\IssueCategoryAndTime()])
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/2/malaflokkar');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly-categories');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::assemblyVoteCategoriesAction
     */
    public function testAssemblyVoteCategoriesAction()
    {
        $this->getMockService(Service\VoteItem::class)
            ->shouldReceive('fetchVoteByAssemblyAndCongressmanAndCategory')
            ->with(1, 2)
            ->once()
            ->andReturn([])
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/2/atvaedagreidslur-malaflokkar');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly-vote-categories');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::assemblyTimesAction
     */
    public function testAssemblyTimesAction()
    {
        $this->getMockService(Store\Congressman::class)
            ->shouldReceive('fetchTimeByAssembly')
            ->with(1, 5, -1)
            ->once()
            ->andReturn([
                (new Model\Congressman())->setCongressmanId(1)
            ]);

        $this->dispatch('/loggjafarthing/1/thingmenn/raedutimar');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly-times');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::assemblySpeechTimeAction
     */
    public function testAssemblySpeechTimeAction()
    {
        $this->getMockService(Store\Congressman::class)
            ->shouldReceive('getSpeechTimeByAssembly')
            ->with(1, 2)
            ->once()
            ->andReturn(new Model\ValueAndCount());

        $this->dispatch('/loggjafarthing/1/thingmenn/2/raedutimar');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly-speech-time');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::assemblySpeechTimeAction
     */
    public function testAssemblySpeechTimeActionNotFound()
    {
        $this->getMockService(Store\Congressman::class)
            ->shouldReceive('getSpeechTimeByAssembly')
            ->with(1, 2)
            ->once()
            ->andReturn(null);

        $this->dispatch('/loggjafarthing/1/thingmenn/2/raedutimar');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly-speech-time');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::assemblyQuestionsAction
     */
    public function testAssemblyQuestionsAction()
    {
        $this->getMockService(Store\Congressman::class)
            ->shouldReceive('fetchQuestionByAssembly')
            ->with(1, 5, -1)
            ->once()
            ->andReturn([
                (new Model\Congressman())->setCongressmanId(1)
            ]);

        $this->dispatch('/loggjafarthing/1/thingmenn/fyrirspurnir');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly-questions');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::assemblyPropositionsAction
     */
    public function testAssemblyResolutionsAction()
    {
        $this->getMockService(Store\Congressman::class)
            ->shouldReceive('fetchPropositionsByAssembly')
            ->with(1, 5)
            ->once()
            ->andReturn([
                (new Model\Congressman())->setCongressmanId(1)
            ]);

        $this->dispatch('/loggjafarthing/1/thingmenn/thingsalyktanir');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly-propositions');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::assemblyBillsAction
     */
    public function testAssemblyBillsAction()
    {
        $this->getMockService(Store\Congressman::class)
            ->shouldReceive('fetchBillsByAssembly')
            ->with(1, 5, -1)
            ->once()
            ->andReturn([
                (new Model\Congressman())->setCongressmanId(1)
            ]);

        $this->dispatch('/loggjafarthing/1/thingmenn/lagafrumvorp');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly-bills');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::assemblyIssuesSummaryAction
     */
    public function testAssemblyIssuesSummaryAction()
    {
        $this->getMockService(Store\Issue::class)
            ->shouldReceive('fetchByAssemblyAndCongressmanSummary')
            ->with(1, 2)
            ->andReturn([
                (new Model\Issue())
            ])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/2/thingmal-samantekt');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly-issues-summary');
        $this->assertResponseStatusCode(206);
    }
}
