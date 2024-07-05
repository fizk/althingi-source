<?php

namespace Althingi\Controller;

use Althingi\Controller\CommitteeController;
use Althingi\{Model, Service};
use Althingi\ServiceHelper;
use Library\Container\Container;
use Mockery;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(CommitteeController::class)]
#[CoversMethod(CommitteeController::class, 'setCommitteeService')]
#[CoversMethod(CommitteeController::class, 'get')]
#[CoversMethod(CommitteeController::class, 'getList')]
#[CoversMethod(CommitteeController::class, 'options')]
#[CoversMethod(CommitteeController::class, 'optionsList')]
#[CoversMethod(CommitteeController::class, 'patch')]
#[CoversMethod(CommitteeController::class, 'put')]
class CommitteeControllerTest extends TestCase
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
    public function getOneCommitteeSuccessfully()
    {
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('get')
            ->once()
            ->with(1)
            ->andReturn((new Model\Committee())->setCommitteeId(1)->setFirstAssemblyId(1))
            ->getMock();

        $this->dispatch('/nefndir/1', 'GET');

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getOneCommitteeWhichIsNotFoundError()
    {
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('get')
            ->once()
            ->with(1)
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/nefndir/1', 'GET');

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function fetchAllCommittiesSuccessfully()
    {
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                (new Model\Committee())->setCommitteeId(1)->setFirstAssemblyId(1),
                (new Model\Committee())->setCommitteeId(2)->setFirstAssemblyId(1),
                (new Model\Committee())->setCommitteeId(3)->setFirstAssemblyId(1),
            ])
            ->getMock();

        $this->dispatch('/nefndir', 'GET');

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function putOneCommitteeSavingItSuccessfully()
    {
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/nefndir/1', 'PUT', [
            'first_assembly_id' => 1,
            'last_assembly_id' => 1,
            'name' => 'name',
            'abbr_short' => 'n',
            'abbr_long' => 'na',

        ]);

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function putNewCommitteeWithTheIDOfZeroSuccessfully()
    {
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/nefndir/0', 'PUT', [
            'first_assembly_id' => 1,
            'last_assembly_id' => 1,
            'name' => 'name',
            'abbr_short' => 'n',
            'abbr_long' => 'na',

        ]);

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function putNewCommitteeWithNegativeIDSuccessfully()
    {
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/nefndir/-1', 'PUT', [
            'first_assembly_id' => 1,
            'last_assembly_id' => 1,
            'name' => 'name',
            'abbr_short' => 'n',
            'abbr_long' => 'na',

        ]);

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function putCreateNewCommitteeButGetAnError()
    {
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('create')
            ->never()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/nefndir/1', 'PUT', []);

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    #[Test]
    public function patchCommitteeWithSubsetOfDataSuccessfully()
    {
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new Model\Committee())
                ->setCommitteeId(1)
                ->setFirstAssemblyId(1)
                ->setLastAssemblyId(1)
                ->setName('name')
                ->setAbbrShort('n')
                ->setAbbrLong('na')
            )
            ->getMock()
            ->shouldReceive('update')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/nefndir/1', 'PATCH', [
            'first_assembly_id' => 1,
        ]);

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    #[Test]
    public function patchCommitteeButTheValuesAreIncorrectError()
    {
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new Model\Committee())
                ->setCommitteeId(1)
                ->setFirstAssemblyId(1)
                ->setLastAssemblyId(1)
                ->setAbbrShort('n')
                ->setAbbrLong('na')
            )
            ->getMock()
            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/nefndir/1', 'PATCH', [
            'first_assembly_id' => 1,
        ]);

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    #[Test]
    public function patchCommitteeButTheCommitteeDoesNotExistError()
    {
        $this->getMockService('Althingi\Service\Committee')
            ->shouldReceive('get')
            ->once()
            ->andReturn(null)
            ->getMock()
            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/nefndir/1', 'PATCH', [
            'first_assembly_id' => 1,
        ]);

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function optionsGetAllowedMethods()
    {
        $this->dispatch('/nefndir/1', 'OPTIONS');

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('options');
        $this->assertResponseStatusCode(200);
        $this->assertHasResponseHeader('Allow');
    }

    #[Test]
    public function optionsGetListOfAllowedMethods()
    {
        $this->dispatch('/nefndir', 'OPTIONS');

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('optionsList');
        $this->assertResponseStatusCode(200);
        $this->assertHasResponseHeader('Allow');
    }
}
