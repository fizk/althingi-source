<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 14/06/15
 * Time: 2:51 AM
 */

namespace Althingi\Controller;

use \Althingi\Model\Constituency as ConstituencyModel;
use Althingi\Service\Constituency;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class ConstituencyControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\ConstituencyController
 * @covers \Althingi\Controller\ConstituencyController::setConstituencyService
 */
class ConstituencyControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Constituency::class,

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
        $this->getMockService(Constituency::class)
            ->shouldReceive('create')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/kjordaemi/1', 'PUT', [
            'name' => 'name1'
        ]);
        $this->assertResponseStatusCode(201);
        $this->assertControllerClass('ConstituencyController');
        $this->assertActionName('put');
    }

    /**
     * @covers ::put
     */
    public function testPutInvalidForm()
    {
        $this->getMockService(Constituency::class)
            ->shouldReceive('create')
            ->never()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/kjordaemi/1', 'PUT');
        $this->assertResponseStatusCode(400);
        $this->assertControllerClass('ConstituencyController');
        $this->assertActionName('put');
    }

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $expectedData = (new ConstituencyModel())
            ->setConstituencyId(101)
            ->setName('name1');

        $this->getMockService(Constituency::class)
            ->shouldReceive('get')
            ->with(101)
            ->andReturn(
                (new ConstituencyModel())
                    ->setConstituencyId(101)
                    ->setName('some name')
            )
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->with(\Mockery::on(function ($actualDate) use ($expectedData) {
                return $actualDate == $expectedData;
            }))
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/kjordaemi/101', 'PATCH', [
            'name' => 'name1'
        ]);
        $this->assertResponseStatusCode(205);
        $this->assertControllerClass('ConstituencyController');
        $this->assertActionName('patch');
    }

    /**
     * @covers ::patch
     */
    public function testPatchNotFound()
    {
        $this->getMockService(Constituency::class)
            ->shouldReceive('get')
            ->with(101)
            ->andReturn(null)
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/kjordaemi/101', 'PATCH', [
            'name' => 'name1'
        ]);
        $this->assertResponseStatusCode(404);
        $this->assertControllerClass('ConstituencyController');
        $this->assertActionName('patch');
    }
}
