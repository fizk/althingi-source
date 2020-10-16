<?php

namespace AlthingiTest\Controller;

use Althingi\Model\President;
use Althingi\Service\Congressman;
use Althingi\Service\Party;
use AlthingiTest\ServiceHelper;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class PresidentAssemblyControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\PresidentAssemblyController
 *
 * @covers \Althingi\Controller\PresidentAssemblyController::setPartyService
 * @covers \Althingi\Controller\PresidentAssemblyController::setCongressmanService
 */
class PresidentAssemblyControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );

        parent::setUp();

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
            ->andReturn([(new President())->setPresidentId(1)->setFrom(new \DateTime())])
            ->once()
            ->getMock();

        $this->getMockService(Party::class)
            ->shouldReceive('getByCongressman')
            ->andReturn((new \Althingi\Model\Party()))
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/forsetar', 'GET');
        $this->assertControllerClass('PresidentAssemblyController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
        $this->assertResponseHeaderContains('Content-Range', 'items 0-1/1');
        $this->assertResponseHeaderContains('Range-Unit', 'items');
    }
}
