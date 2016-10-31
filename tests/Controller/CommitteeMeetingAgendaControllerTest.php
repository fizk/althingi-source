<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class CommitteeMeetingAgendaControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );
        parent::setUp();
    }

    public function testPutSuccess()
    {
        $pdo = \Mockery::mock('PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->shouldReceive('rowCount')
            ->andReturn(1)
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646/dagskralidir/1', 'PUT', [
            'title' => 'some description'
        ]);

        $this->assertControllerClass('CommitteeMeetingAgendaController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    public function testPutSuccessWithIssue()
    {
        $pdo = \Mockery::mock('PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->shouldReceive('rowCount')
            ->andReturn(1)
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646/dagskralidir/1', 'PUT', [
            'title' => 'some description',
            'issue_id' => 1
        ]);

        $this->assertControllerClass('CommitteeMeetingAgendaController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }
}
