<?php

namespace Althingi\Controller;

use Althingi\Controller\AssemblyCommitteeController;
use Althingi\{Model, Service};
use Althingi\ServiceHelper;
use Library\Container\Container;
use Mockery;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(AssemblyCommitteeController::class)]
#[CoversMethod(AssemblyCommitteeController::class, 'setCommitteeService')]
#[CoversMethod(AssemblyCommitteeController::class, 'get')]
#[CoversMethod(AssemblyCommitteeController::class, 'getList')]
class AssemblyCommitteeControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );

        $this->buildServices([
            Service\Committee::class,
        ]);
    }

    #[After]
    public function down(): void
    {
        $this->destroyServices();
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function getOneCommittee()
    {
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('get')
            ->withArgs([1])
            ->andReturn((new Model\Committee())->setCommitteeId(1)->setFirstAssemblyId(1))
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144/nefndir/1', 'GET');

        $this->assertControllerName(AssemblyCommitteeController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getOneCommitteeThatIsNotFound()
    {
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('get')
            ->withArgs([1])
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144/nefndir/1', 'GET');

        $this->assertControllerName(AssemblyCommitteeController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function getAListOfCommitteesForGivenAssembly()
    {
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('fetchByAssembly')
            ->withArgs([144])
            ->andReturn([
                (new Model\Committee())->setCommitteeId(1)->setFirstAssemblyId(100),
                (new Model\Committee())->setCommitteeId(2)->setFirstAssemblyId(100),
                (new Model\Committee())->setCommitteeId(3)->setFirstAssemblyId(100),
                (new Model\Committee())->setCommitteeId(4)->setFirstAssemblyId(100),
            ])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144/nefndir', 'GET');

        $this->assertControllerName(AssemblyCommitteeController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }
}
