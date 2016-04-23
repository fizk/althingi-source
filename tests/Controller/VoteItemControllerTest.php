<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Mockery;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class VoteItemControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );
        parent::setUp();
    }

    public function testPostSuccess()
    {
        $voteItemService = Mockery::mock('Althingi\Service\VoteItem')
            ->shouldReceive('create')
            ->andReturn([])
            ->once()
            ->getMock();
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\VoteItem', $voteItemService);

        $this->dispatch('/loggjafarthing/1/thingmal/2/atkvaedagreidslur/3/atkvaedi', 'POST', [
            'congressman_id' => 1,
            'vote' => 'nei'
        ]);

        $this->assertControllerClass('VoteItemController');
        $this->assertActionName('post');
        $this->assertResponseStatusCode(201);
    }

    public function testPostFail()
    {
        $voteItemService = Mockery::mock('Althingi\Service\VoteItem')
            ->shouldReceive('create')
            ->andReturnUsing(function ($data) {
                $this->assertEquals(3, $data->vote_id);
                return [];
            })
            ->once()
            ->getMock();
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\VoteItem', $voteItemService);

        $this->dispatch('/loggjafarthing/1/thingmal/2/atkvaedagreidslur/3/atkvaedi', 'POST');

        $this->assertControllerClass('VoteItemController');
        $this->assertActionName('post');
        $this->assertResponseStatusCode(400);
    }
}
