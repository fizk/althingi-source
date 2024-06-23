<?php

namespace Althingi\Controller;

use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\TestCase;

/**
 * Class AssemblyCommitteeControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\IndexController
 */
class IndexControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
    }

    /**
     * @covers ::handle
     */
    public function testIndex()
    {
        $this->dispatch('/', 'GET');

        $this->assertControllerName(\Althingi\Controller\IndexController::class);
        $this->assertResponseStatusCode(200);
    }
}
