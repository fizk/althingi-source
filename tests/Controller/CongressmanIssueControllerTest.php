<?php

namespace Althingi\Controller;

use Althingi\Controller\CongressmanIssueController;
use Althingi\{Model, Service};
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(CongressmanIssueController::class)]
#[CoversMethod(CongressmanIssueController::class, 'setIssueService')]
#[CoversMethod(CongressmanIssueController::class, 'getList')]
class CongressmanIssueControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Issue::class,

        ]);
    }

    #[After]
    public function down(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function getCongressmanIssueList()
    {
        $this->getMockService(Service\Issue::class)
            ->shouldReceive('fetchByCongressman')
            ->with(123)
            ->once()
            ->andReturn([(new Model\Issue())->setKind(Model\KindEnum::A)])
            ->getMock();

        $this->dispatch('/thingmenn/123/thingmal', 'GET');

        $this->assertControllerName(CongressmanIssueController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }
}
