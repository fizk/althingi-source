<?php

namespace Althingi\Controller;

use Althingi\{Model, Service};
use Althingi\Controller\PresidentAssemblyController;
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(PresidentAssemblyController::class)]
#[CoversMethod(PresidentAssemblyController::class, 'setCongressmanService')]
#[CoversMethod(PresidentAssemblyController::class, 'getList')]

class PresidentAssemblyControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Party::class,
            Service\Congressman::class,
        ]);
    }

    #[After]
    public function down(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function getList()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('fetchPresidentsByAssembly')
            ->once()
            ->andReturn([
                (new Model\President())
                    ->setPresidentId(1)
                    ->setFrom(new \DateTime())
                    ->setCongressmanId(1)
                    ->setAssemblyId(1)
                    ->setTitle('title')
            ])
            ->getMock();

        $this->dispatch('/loggjafarthing/1/forsetar', 'GET');
        $this->assertControllerName(PresidentAssemblyController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }
}
