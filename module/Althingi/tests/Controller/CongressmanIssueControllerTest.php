<?php

namespace AlthingiTest\Controller;

use Althingi\Service\Issue;
use \Althingi\Model\Issue as IssueModel;
use AlthingiTest\ServiceHelper;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class CongressmanIssueControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CongressmanIssueController
 *
 * @covers \Althingi\Controller\CongressmanIssueController::setIssueService
 */
class CongressmanIssueControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );

        parent::setUp();

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
            ->andReturn([new IssueModel()])
            ->getMock();

        $this->dispatch('/thingmenn/123/thingmal', 'GET');

        $this->assertControllerClass('CongressmanIssueController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
        $this->assertResponseHeaderContains('Content-Range', 'items 0-1/1');
        $this->assertResponseHeaderContains('Range-Unit', 'items');
    }
}
