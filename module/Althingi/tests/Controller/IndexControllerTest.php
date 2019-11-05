<?php

namespace AlthingiTest\Controller;

use Zend\Cache\Storage\StorageInterface;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class AssemblyCommitteeControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\IndexController
 */
class IndexControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = false;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );
    }

    /**
     * @covers ::indexAction
     */
    public function testIndex()
    {
        $this->dispatch('/', 'GET');

        $this->assertControllerName(\Althingi\Controller\IndexController::class);
        $this->assertActionName('index');
        $this->assertResponseStatusCode(200);
    }
}
