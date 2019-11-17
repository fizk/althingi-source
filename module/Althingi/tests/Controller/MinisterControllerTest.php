<?php

namespace AlthingiTest\Controller;

use Althingi\Service;
use Althingi\Store;
use Althingi\Model;
use Althingi\Controller;

use AlthingiTest\ServiceHelper;
use Mockery;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class IssueControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\MinisterController
 *
 * @covers \Althingi\Controller\MinisterController::setMinistryService
 */
class MinisterControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Service\Issue::class,
            Service\Assembly::class,
            Service\Category::class,
            Store\Issue::class,
            Store\Category::class,
        ]);
    }

    public function tearDown()
    {
        \Mockery::close();
        return parent::tearDown();
    }

    /**
     * @covers ::get
     * @throws \Exception
     */
    public function testTrue()
    {
        $this->dispatch('/loggjafarthing/149/thingmenn/1335/radherra', 'GET');

        $this->assertControllerName(Controller\MinisterController::class);
        $this->assertActionName('getList');
    }
}
