<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Althingi\Service\Cabinet;
use Althingi\Service\Congressman;
use Althingi\Service\Party;
use DateTime;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class CabinetControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CabinetController
 * @covers \Althingi\Controller\CabinetController::setCongressmanService
 * @covers \Althingi\Controller\CabinetController::setPartyService
 * @covers \Althingi\Controller\CabinetController::setCabinetService
 */
class CabinetControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );
        parent::setUp();
        $this->buildServices([
            Congressman::class,
            Party::class,
            Cabinet::class,
        ]);
    }

    public function tearDown()
    {
        \Mockery::close();
        $this->destroyServices();
        return parent::tearDown();
    }

    /**
     * @covers ::assemblyAction
     */
    public function testGet()
    {
        $this->getMockService(Cabinet::class)
            ->shouldReceive('fetchByAssembly')
            ->andReturn([
                (new \Althingi\Model\Cabinet())->setCabinetId(1)
            ])
            ->getMock();

        $this->getMockService(Party::class)
            ->shouldReceive('getByCongressman')
            ->andReturn((new \Althingi\Model\Party())->setPartyId(1))
            ->getMock();

        $this->getMockService(Congressman::class)
            ->shouldReceive('fetchByCabinet')
            ->andReturn([
                (new \Althingi\Model\CongressmanAndCabinet())
                    ->setDate(new DateTime('2000-01-01'))
                    ->setCongressmanId(1)
                    ->setBirth(new DateTime('2001-01-01'))
            ])
            ->getMock();

        $this->dispatch('/loggjafarthing/1/raduneyti', 'GET');

        $this->assertControllerClass('CabinetController');
        $this->assertActionName('assembly');
        $this->assertResponseStatusCode(200);

//        print_r(json_decode($this->getResponse()->getContent()));
    }
}
