<?php

namespace AlthingiTest\Controller;

use Althingi\Controller\PresidentAssemblyController;
use Althingi\Model\President;
use Althingi\Service\Congressman;
use Althingi\Service\Party;
use AlthingiTest\ServiceHelper;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * Class PresidentAssemblyControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\PresidentAssemblyController
 *
 * @covers \Althingi\Controller\PresidentAssemblyController::setPartyService
 * @covers \Althingi\Controller\PresidentAssemblyController::setCongressmanService
 */
class PresidentAssemblyControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new ServiceManager(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Party::class,
            Congressman::class,
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
    public function testGetList()
    {
        $this->getMockService(Congressman::class)
            ->shouldReceive('fetchPresidentsByAssembly')
            ->andReturn([
                (new President())
                    ->setPresidentId(1)
                    ->setFrom(new \DateTime())
                    ->setCongressmanId(1)
                    ->setAssemblyId(1)
                    ->setTitle('title')
            ])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/forsetar', 'GET');
        $this->assertControllerName(PresidentAssemblyController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }
}
