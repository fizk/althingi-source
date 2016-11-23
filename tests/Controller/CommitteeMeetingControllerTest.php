<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class CommitteeMeetingControllerTest extends AbstractHttpControllerTestCase
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

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646', 'PUT', [
            'from' => "2016-04-26 13:00:00",
            'to' => "2016-04-26 15:10:00",
            'description' => 'some description'
        ]);

        $this->assertControllerClass('CommitteeMeetingController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    public function testPatchSuccess()
    {
        $pdo = \Mockery::mock('PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->shouldReceive('fetchObject')
            ->andReturn((object)[
                'from' => '',
                'to' => '',
                'description' => '',
                'committee_id' => 202,
                'committee_meeting_id' => 1646,
                'assembly_id' => 145,
            ])
            ->shouldReceive('rowCount')
            ->andReturn(1)
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646', 'PATCH', [
            'from' => "2016-04-26 13:00:00",
            'to' => "2016-04-26 15:10:00",
            'description' => 'some description'
        ]);

        $this->assertControllerClass('CommitteeMeetingController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    public function testPatchInvalidDate()
    {
        $pdo = \Mockery::mock('PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->shouldReceive('fetchObject')
            ->andReturn((object)[
                'from' => '',
                'to' => '',
                'description' => '',
                'committee_id' => 202,
                'committee_meeting_id' => 1646,
                'assembly_id' => 145,
            ])
            ->shouldReceive('rowCount')
            ->andReturn(1)
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646', 'PATCH', [
            'from' => "This is not a valid date string",
            'to' => "This is not a valid date string",
            'description' => 'some description'
        ]);

        $this->assertControllerClass('CommitteeMeetingController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    public function testPatchResourceNotFound()
    {
        $pdo = \Mockery::mock('PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->shouldReceive('fetchObject')
            ->andReturn(false)
            ->shouldReceive('rowCount')
            ->andReturn(1)
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646', 'PATCH', [
            'from' => "2016-04-26 13:00:00",
            'to' => "2016-04-26 15:10:00",
            'description' => 'some description'
        ]);

        $this->assertControllerClass('CommitteeMeetingController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    public function testGetSuccess()
    {
        $pdo = \Mockery::mock('PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->shouldReceive('fetchObject')
            ->andReturn((object)[
                'from' => '',
                'to' => '',
                'description' => '',
                'committee_id' => 202,
                'committee_meeting_id' => 1646,
                'assembly_id' => 145,
            ])
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646', 'GET');

        $this->assertControllerClass('CommitteeMeetingController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    public function testGetResourceNotFound()
    {
        $pdo = \Mockery::mock('PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->shouldReceive('fetchObject')
            ->andReturn(false)
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdo);

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646', 'GET');

        $this->assertControllerClass('CommitteeMeetingController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }
}
