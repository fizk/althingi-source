<?php

namespace AlthingiTest\Controller;

use Althingi\Service\Congressman;
use Althingi\Service\Issue;
use Althingi\Service\Party;
use Althingi\Service\Plenary;
use Althingi\Service\PlenaryAgenda;
use AlthingiTest\ServiceHelper;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class PlenaryControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\PlenaryAgendaController
 * @covers \Althingi\Controller\PlenaryAgendaController::setPlenaryService
 * @covers \Althingi\Controller\PlenaryAgendaController::setPlenaryAgendaService
 * @covers \Althingi\Controller\PlenaryAgendaController::setCongressmanService
 * @covers \Althingi\Controller\PlenaryAgendaController::setIssueService
 * @covers \Althingi\Controller\PlenaryAgendaController::setPartyService
 */
class PlenaryAgendaControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            PlenaryAgenda::class,
            Plenary::class,
            Issue::class,
            Congressman::class,
            Party::class,
        ]);
    }

    public function tearDown()
    {
        \Mockery::close();
        return parent::tearDown();
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

        $this->assertControllerName(\Althingi\Controller\PlenaryAgendaController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $expectedData = (new \Althingi\Model\PlenaryAgenda())
            ->setAssemblyId(1)
            ->setIssueId(2)
            ->setCategory('A')
            ->setPlenaryId(3)
            ->setItemId(4)
            ->setComment('This is the comment')
        ;
        $this->getMockService(PlenaryAgenda::class)
            ->shouldReceive('get')
            ->andReturn(
                (new \Althingi\Model\PlenaryAgenda())
                    ->setAssemblyId(1)
                    ->setIssueId(2)
                    ->setCategory('A')
                    ->setPlenaryId(3)
                    ->setItemId(4)
            )
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->with(\Mockery::on(function ($actualData) use ($expectedData) {
                return $expectedData == $actualData;
            }))
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingfundir/3/lidir/4', 'PATCH', [
            'comment' => 'This is the comment'
        ]);

        $this->assertControllerClass('PlenaryAgendaController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }
}
