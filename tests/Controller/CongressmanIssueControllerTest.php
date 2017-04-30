<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 3/07/15
 * Time: 8:05 AM
 */

namespace Althingi\Controller;

use Althingi\Service\Issue;
use \Althingi\Model\Issue as IssueModel;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class CongressmanIssueControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CongressmanIssueController
 * @covers \Althingi\Controller\CongressmanIssueController::setIssueService
 */
class CongressmanIssueControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Issue::class,

        ]);
    }

    public function tearDown()
    {
        \Mockery::close();
        return parent::tearDown();
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
        $this->assertResponseStatusCode(200);
    }
}
