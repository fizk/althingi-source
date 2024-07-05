<?php

namespace Althingi\Controller;

use Althingi\{Model, Service};
use Althingi\Controller\PlenaryAgendaController;
use Althingi\ServiceHelper;
use Library\Container\Container;
use PDOException;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(PlenaryAgendaController::class)]
#[CoversMethod(PlenaryAgendaController::class, 'setPlenaryAgendaService')]
#[CoversMethod(PlenaryAgendaController::class, 'getList')]
#[CoversMethod(PlenaryAgendaController::class, 'patch')]
#[CoversMethod(PlenaryAgendaController::class, 'put')]
class PlenaryAgendaControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\PlenaryAgenda::class,
            Service\Plenary::class,
            Service\Issue::class,
            Service\Congressman::class,
            Service\Party::class,
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
        $this->getMockService(Service\PlenaryAgenda::class)
            ->shouldReceive('fetch')
            ->with(1, 2)
            ->andReturn([
                (new Model\PlenaryAgenda())
                    ->setIssueId(10)
                    ->setAssemblyId(1)
                    ->setKind(Model\KindEnum::A)
                    ->setPlenaryId(2)
            ])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingfundir/2/lidir');
        $this->assertControllerName(PlenaryAgendaController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function putSuccess()
    {
        $expectedData = (new Model\PlenaryAgenda())
            ->setAssemblyId(1)
            ->setPlenaryId(2)
            ->setKind(Model\KindEnum::B)
            ->setIssueId(1)
            ->setItemId(1)
        ;
        $this->getMockService(Service\PlenaryAgenda::class)
            ->shouldReceive('save')
            ->with(\Mockery::on(function ($actualData) use ($expectedData) {
                return $expectedData == $actualData;
            }))
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingfundir/2/lidir/1', 'PUT', [
            'issue_id' => 1,
            'kind' => 'B',
        ]);

        $this->assertControllerName(PlenaryAgendaController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function putIssueNotFound()
    {
        $this->getMockService(Service\PlenaryAgenda::class)
            ->shouldReceive('save')
            ->once()
            ->andThrow(new PDOException('e_id`, `assembly_id`, `category`) REFERENCES `Issue` (`issue_id`', 23000))
            ->getMock()
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->getMockService(Service\Issue::class)
            ->shouldReceive('create')
            ->andReturns(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingfundir/2/lidir/1', 'PUT', [
            'issue_id' => 1,
            'kind' => 'B',
            'issue_name' => '',
            'issue_type' => '',
            'issue_typename' => '',
        ]);

        $this->assertControllerName(PlenaryAgendaController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function patchSuccess()
    {
        $this->dispatch('/loggjafarthing/1/thingfundir/3/lidir/4', 'PATCH', [
            'comment' => 'This is the comment'
        ]);

        $this->assertControllerName(PlenaryAgendaController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(202);
    }
}
