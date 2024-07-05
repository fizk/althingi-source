<?php

namespace Althingi\Controller;

use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before};
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexController::class)]
#[CoversMethod(IndexController::class, 'handle')]
class IndexControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
    }

    #[Test]
    public function indexSuccessful()
    {
        $this->dispatch('/', 'GET');

        $this->assertControllerName(\Althingi\Controller\IndexController::class);
        $this->assertResponseStatusCode(200);
    }
}
