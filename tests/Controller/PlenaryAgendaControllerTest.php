<?php

namespace Althingi\Controller;

use Althingi\Controller\PlenaryAgendaController;
use Althingi\Model;
use Althingi\Service\Congressman;
use Althingi\Service\Issue;
use Althingi\Service\Party;
use Althingi\Service\Plenary;
use Althingi\Service\PlenaryAgenda;
use Althingi\ServiceHelper;
use Library\Container\Container;
use PDOException;
use PHPUnit\Framework\TestCase;

/**
 * Class PlenaryControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\PlenaryAgendaController
 *
 * @covers \Althingi\Controller\PlenaryAgendaController::setPlenaryAgendaService
 */
class PlenaryAgendaControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            PlenaryAgenda::class,
            Plenary::class,
            Issue::class,
            Congressman::class,
            Party::class,
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
        $this->getMockService(PlenaryAgenda::class)
            ->shouldReceive('fetch')
            ->with(1, 2)
            ->andReturn([
                (new Model\PlenaryAgenda())
                    ->setIssueId(10)
                    ->setAssemblyId(1)
                    ->setCategory('a')
                    ->setPlenaryId(2)
            ])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingfundir/2/lidir');
        $this->assertControllerName(PlenaryAgendaController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::put
     */
    public function testPutSuccess()
    {
        $expectedData = (new \Althingi\Model\PlenaryAgenda())
            ->setAssemblyId(1)
            ->setPlenaryId(2)
            ->setCategory('B')
            ->setIssueId(1)
            ->setItemId(1)
        ;
        $this->getMockService(PlenaryAgenda::class)
            ->shouldReceive('save')
            ->with(\Mockery::on(function ($actualData) use ($expectedData) {
                return $expectedData == $actualData;
            }))
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingfundir/2/lidir/1', 'PUT', [
            'issue_id' => 1,
            'category' => 'B',
        ]);

        $this->assertControllerName(PlenaryAgendaController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::put
     */
    public function testPutIssueNotFound()
    {
        $expectedData = (new \Althingi\Model\PlenaryAgenda())
            ->setAssemblyId(1)
            ->setPlenaryId(2)
            ->setCategory('B')
            ->setIssueId(1)
            ->setItemId(1)
        ;
        $this->getMockService(PlenaryAgenda::class)
            ->shouldReceive('save')
            ->once()
            ->andThrow(new PDOException('e_id`, `assembly_id`, `category`) REFERENCES `Issue` (`issue_id`', 23000))
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->getMockService(Issue::class)
            ->shouldReceive('create')
            ->andReturns(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingfundir/2/lidir/1', 'PUT', [
            'issue_id' => 1,
            'category' => 'B',
            'issue_name' => '',
            'issue_type' => '',
            'issue_typename' => '',
        ]);

        $this->assertControllerName(PlenaryAgendaController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $this->dispatch('/loggjafarthing/1/thingfundir/3/lidir/4', 'PATCH', [
            'comment' => 'This is the comment'
        ]);

        $this->assertControllerName(PlenaryAgendaController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(202);
    }
}
