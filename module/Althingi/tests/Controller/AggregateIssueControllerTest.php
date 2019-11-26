<?php

namespace AlthingiTest\Controller;

use Althingi\Model\Assembly as AssemblyModel;
use Althingi\Model\Cabinet as CabinetModel;
use Althingi\Service\Assembly;
use Althingi\Service\CongressmanDocument;
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
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class AssemblyControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\Aggregate\IssueController
 *
 * @covers \Althingi\Controller\Aggregate\IssueController::setIssueService

 */
class AggregateIssueControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Issue::class,
        ]);
    }

    public function tearDown()
    {
        $this->destroyServices();
        \Mockery::close();
        return parent::tearDown();
    }

    /**
     * @covers ::progressAction
     */
    public function testProgress()
    {
        $this->getMockService(Issue::class)
            ->shouldReceive('fetchProgress')
            ->once()
            ->getMock();

        $this->dispatch('/samantekt/loggjafarthing/1/thingmal/a/2/ferill', 'GET');

        $this->assertControllerClass('IssueController');
        $this->assertActionName('progress');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::countTypeStatusAction
     */
    public function testCountTypeAction()
    {
        $this->getMockService(Issue::class)
            ->shouldReceive('fetchCountByCategoryAndStatus')
            ->once()
            ->getMock();

        $this->dispatch('/samantekt/loggjafarthing/1/thingmal/flokkar-stada', 'GET');

        $this->assertControllerClass('IssueController');
        $this->assertActionName('count-type-status');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::countGovernmentAction
     */
    public function testCountGovernmentAction()
    {
        $this->getMockService(Issue::class)
            ->shouldReceive('fetchCountByGovernment')
            ->once()
            ->getMock();

        $this->dispatch('/samantekt/loggjafarthing/1/thingmal/stjornarfrumvorp', 'GET');

        $this->assertControllerClass('IssueController');
        $this->assertActionName('count-government');
        $this->assertResponseStatusCode(206);
    }
}
