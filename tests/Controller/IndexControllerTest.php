<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Mockery;
use Althingi\Service\Committee;
use Althingi\Model\Committee as CommitteeModel;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class AssemblyCommitteeControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\IndexController
 */
class IndexControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );

        parent::setUp();

        $this->buildServices([]);
    }

    public function tearDown()
    {
        $this->destroyServices();
        Mockery::close();
        return parent::tearDown();
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
