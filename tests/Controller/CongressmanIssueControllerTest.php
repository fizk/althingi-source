<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 3/07/15
 * Time: 8:05 AM
 */

namespace Althingi\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class CongressmanIssueControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );
        parent::setUp();
    }

    public function testTrue()
    {
        $serviceMock = \Mockery::mock('Althingi\Service\Issue')
            ->shouldReceive('fetchByCongressman')
            ->andReturnUsing(function ($id) {
                $this->assertEquals('123', $id);
                return [];
            })
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Issue', $serviceMock);

        $this->dispatch('/api/thingmenn/123/thingmal', 'GET');

        $this->assertControllerClass('CongressmanIssueController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(200);
    }
}
