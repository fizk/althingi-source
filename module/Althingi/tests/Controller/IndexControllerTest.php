<?php

namespace AlthingiTest\Controller;

use AlthingiTest\ServiceHelper;
use Mockery;
use Althingi\Service\Committee;
use Althingi\Model\Committee as CommitteeModel;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\Stdlib\ArrayUtils;

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
        // The module configuration should still be applicable for tests.
        // You can override configuration here with test case specific values,
        // such as sample view templates, path stacks, module_listener_options,
        // etc.
        $configOverrides = [];

        $this->setApplicationConfig(ArrayUtils::merge(
            // Grabbing the full application configuration:
            include __DIR__ .'/../../../../config/application.config.php',
            $configOverrides
        ));
        parent::setUp();
    }

    /**
     * @covers ::indexAction
     */
    public function testIndex()
    {
        $this->dispatch('/', 'GET');

        $this->assertControllerClass('IndexController');
        $this->assertActionName('index');
        $this->assertResponseStatusCode(200);
    }
}
