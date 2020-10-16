<?php

namespace AlthingiTest\Controller;

use Althingi\Model\Assembly as AssemblyModel;
use Althingi\Model\Cabinet as CabinetModel;
use Althingi\Service\Assembly;
use Althingi\Service\Congressman;
use Althingi\Service\CongressmanDocument;
use Althingi\Service\Constituency;
use Althingi\Service\Document;
use Althingi\Service\President;
use Althingi\Service\Issue;
use Althingi\Service\Party;
use Althingi\Service\Vote;
use Althingi\Service\Speech;
use Althingi\Service\Cabinet;
use Althingi\Service\Category;
use Althingi\Service\Election;
use AlthingiTest\ServiceHelper;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class AssemblyControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\Aggregate\CongressmanController
 *
 * @covers \Althingi\Controller\Aggregate\CongressmanController::setPartyService
 * @covers \Althingi\Controller\Aggregate\CongressmanController::setCongressmanService
 * @covers \Althingi\Controller\Aggregate\CongressmanController::setConstituencyService

 */
class AggregateCongressmanControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Congressman::class,
            Party::class,
            Constituency::class
        ]);
    }

    public function tearDown(): void
    {
        $this->destroyServices();
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * @covers ::getAction
     */
    public function testGetAction()
    {
        $this->getMockService(Congressman::class)
            ->shouldReceive('get')
            ->once()
            ->getMock();

        $this->dispatch('/samantekt/thingmenn/1', 'GET');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::partyAction
     */
    public function testPartyAction()
    {
        $this->getMockService(Party::class)
            ->shouldReceive('fetchByCongressman')
            ->once()
            ->getMock();

        $this->dispatch('/samantekt/thingmenn/1/thingflokkar', 'GET');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('party');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::partyAction
     */
    public function testPartyActionWithDate()
    {
        $this->getMockService(Party::class)
            ->shouldReceive('getByCongressman')
            ->once()
            ->getMock();

        $this->dispatch('/samantekt/thingmenn/1/thingflokkar?dags=2001-01-01', 'GET');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('party');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::constituencyAction
     */
    public function testConstituencyAction()
    {
        $this->getMockService(Constituency::class)
            ->shouldReceive('fetchByCongressman')
            ->once()
            ->getMock();

        $this->dispatch('/samantekt/thingmenn/1/kjordaemi', 'GET');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('constituency');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::constituencyAction
     */
    public function testConstituencyActionWithDate()
    {
        $this->getMockService(Constituency::class)
            ->shouldReceive('getByCongressman')
            ->once()
            ->getMock();

        $this->dispatch('/samantekt/thingmenn/1/kjordaemi?dags=2001-01-01', 'GET');

        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('constituency');
        $this->assertResponseStatusCode(200);
    }
}
