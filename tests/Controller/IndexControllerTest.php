<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IndexControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );
        parent::setUp();
    }

    public function testIndex()
    {
        $this->dispatch('/');
        $this->assertControllerClass('IndexController');
        $this->assertActionName('index');
        $this->assertResponseStatusCode(200);
    }

    public function testDocs()
    {
        $this->dispatch('/docs');
        $this->assertControllerClass('IndexController');
        $this->assertActionName('docs');
        $this->assertResponseStatusCode(200);
    }
}
