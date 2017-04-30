<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 14/06/15
 * Time: 12:14 AM
 */

namespace Althingi\Controller;

use Althingi\Service\Session;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class CongressmanSessionControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CongressmanSessionController
 */
class CongressmanSessionControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Session::class,

        ]);
    }

    public function tearDown()
    {
        \Mockery::close();
        return parent::tearDown();
    }

    public function testTrue()
    {
        $this->assertTrue(true);
    }

//    public function testGetSuccess()
//    {
//        $this->getMockService(Session::class)
//            ->shouldReceive('get')
//            ->once()
//            ->andReturn(new \Althingi\Model\Session())
//            ->getMock();
//
//        $this->dispatch('/thingmenn/1/thingseta/2');
//        $this->assertResponseStatusCode(200);
//        $this->assertControllerClass('CongressmanSessionController');
//        $this->assertActionName('get');
//    }
//
//    public function testGetInvalid()
//    {
//        $serviceMock = \Mockery::mock()
//            ->shouldReceive('get')
//            ->andReturn(null)
//            ->getMock();
//
//        $serviceManager = $this->getApplicationServiceLocator();
//        $serviceManager->setAllowOverride(true);
//        $serviceManager->setService('Althingi\Service\Session', $serviceMock);
//
//        $this->dispatch('/thingmenn/1/thingseta/2');
//        $this->assertResponseStatusCode(404);
//        $this->assertControllerClass('CongressmanSessionController');
//        $this->assertActionName('get');
//    }
//
//    public function testPatchSuccess()
//    {
//        $serviceMock = \Mockery::mock()
//            ->shouldReceive('get')
//            ->andReturn((object) [
//                'session_id' => 2,
//                'congressman_id' => 1,
//                'assembly_id' => 1,
//                'constituency' => (object) [
//                    'id' => 1
//                ],
//                'party' => (object) [
//                    'id' => 2
//                ],
//                'from' => '2010-01-01'
//            ])
//            ->getMock()
//        ->shouldReceive('update')
//        ->andReturnUsing(function ($object) {
//            $this->assertEquals(1, $object->party_id);
//        })->getMock();
//
//        $serviceManager = $this->getApplicationServiceLocator();
//        $serviceManager->setAllowOverride(true);
//        $serviceManager->setService('Althingi\Service\Session', $serviceMock);
//
//        $this->dispatch('/thingmenn/1/thingseta/2', 'PATCH', [
//            'party_id' => 1
//        ]);
//        $this->assertResponseStatusCode(205);
//        $this->assertControllerClass('CongressmanSessionController');
//        $this->assertActionName('patch');
//    }
//
//    public function testPatchResourceNotFound()
//    {
//        $serviceMock = \Mockery::mock()
//            ->shouldReceive('get')
//            ->andReturn(null)
//            ->getMock()
//            ->shouldReceive('update')
//            ->andReturnUsing(function ($object) {
//                $this->assertEquals(1, $object->party_id);
//            })->getMock();
//
//        $serviceManager = $this->getApplicationServiceLocator();
//        $serviceManager->setAllowOverride(true);
//        $serviceManager->setService('Althingi\Service\Session', $serviceMock);
//
//        $this->dispatch('/thingmenn/1/thingseta/2', 'PATCH', []);
//        $this->assertResponseStatusCode(404);
//        $this->assertControllerClass('CongressmanSessionController');
//        $this->assertActionName('patch');
//    }
//
//    public function testPatchInvalid()
//    {
//        $serviceMock = \Mockery::mock()
//            ->shouldReceive('get')
//            ->andReturn((object) [
//                'session_id' => 2,
//                'congressman_id' => 1,
//                'constituency' => (object) [
//                    'id' => 1
//                ],
//                'party' => (object) [
//                    'id' => 2
//                ],
//                'from' => '2010-01-01'
//            ])
//            ->getMock();
//
//        $serviceManager = $this->getApplicationServiceLocator();
//        $serviceManager->setAllowOverride(true);
//        $serviceManager->setService('Althingi\Service\Session', $serviceMock);
//
//        $this->dispatch('/thingmenn/1/thingseta/2', 'PATCH', [
//            'party_id' => 1,
//            'from' => 'not-valid-date'
//        ]);
//        $this->assertResponseStatusCode(400);
//        $this->assertControllerClass('CongressmanSessionController');
//        $this->assertActionName('patch');
//    }
//
//    public function testDelete()
//    {
//        $serviceMock = \Mockery::mock()
//            ->shouldReceive('delete')
//            ->andReturn(1)
//            ->getMock();
//
//        $serviceManager = $this->getApplicationServiceLocator();
//        $serviceManager->setAllowOverride(true);
//        $serviceManager->setService('Althingi\Service\Session', $serviceMock);
//
//        $this->dispatch('/thingmenn/1/thingseta/2', 'DELETE');
//        $this->assertResponseStatusCode(205);
//        $this->assertControllerClass('CongressmanSessionController');
//        $this->assertActionName('delete');
//    }
}
