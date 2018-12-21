<?php

namespace AlthingiTest\Controller;

use Althingi\Service\Assembly;
use Althingi\Service\Cabinet;
use Althingi\Service\Congressman;
use Althingi\Service\Party;
use AlthingiTest\ServiceHelper;
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
            include __DIR__ .'/../../../../config/application.config.php'
        );
        parent::setUp();
        $this->buildServices([
            Congressman::class,
            Party::class,
            Cabinet::class,
            Assembly::class,
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
            ->shouldReceive('fetchAll')
            ->andReturn([
                (new \Althingi\Model\Cabinet())->setCabinetId(1)
            ])
            ->getMock();

        $this->getMockService(Assembly::class)
            ->shouldReceive('get')
            ->andReturn(
                (new \Althingi\Model\Assembly())
                    ->setFrom(new DateTime('2001-01-01'))
                    ->setTo(new DateTime('2001-01-01'))
                    ->setAssemblyId(1)
            )
            ->getMock();

        $this->dispatch('/loggjafarthing/1/raduneyti', 'GET');

        $this->assertControllerClass('CabinetController');
        $this->assertActionName('assembly');
        $this->assertResponseStatusCode(200);
    }
}
