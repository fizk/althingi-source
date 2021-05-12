<?php

namespace Althingi\Controller;

use Althingi\Controller\CongressmanIssueController;
use Althingi\Service\Issue;
use Althingi\Model;
use Althingi\ServiceHelper;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * Class CongressmanIssueControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CongressmanIssueController
 *
 * @covers \Althingi\Controller\CongressmanIssueController::setIssueService
 */
class CongressmanIssueControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new ServiceManager(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Issue::class,

        ]);
    }

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * @covers ::getList
     */
    public function testGetCongressmanIssueList()
    {
        $this->getMockService(Issue::class)
            ->shouldReceive('fetchByCongressman')
            ->with(123)
            ->once()
            ->andReturn([new Model\Issue()])
            ->getMock();

        $this->dispatch('/thingmenn/123/thingmal', 'GET');

        $this->assertControllerName(CongressmanIssueController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }
}
