<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class LoggjafarthingControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );
        parent::setUp();
    }

    public function rangeProvider()
    {
        return [
            [144, [], 'items 0-25/144'],
            [144, ['Range' => 'items 10-30'], 'items 10-30/144'],
            [144, ['Range' => 'items 0-50'], 'items 0-25/144'],
            [144, ['Range' => 'items 50-100'], 'items 50-75/144'],
            [144, ['Range' => 'items 145-150'], 'items 0-0/144'],
            [144, ['Range' => 'items 20-10'], 'items 0-0/144'],
            [144, ['Range' => 'items 140-150'], 'items 140-144/144'],
            [10, [], 'items 0-10/10'],
            //[144, ['Range' => 'items 1h-vei'], 'items 0-25/144'],
        ];
    }

    /**
     * @param int $size
     * @param array $header
     * @param string $result
     * @dataProvider rangeProvider
     */
    public function testGetList($size, $header, $result)
    {
        $serviceMock = \Mockery::mock('\Althingi\Service\Assembly')
            ->shouldReceive('count')
                ->andReturn($size)
                ->once()
            ->getMock()
            ->shouldReceive('fetchAll')
                ->andReturn([])
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);

        /** @var  $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->getApplication()->getRequest();
        $request->setHeaders((new \Zend\Http\Headers())->addHeaders($header));
        $this->dispatch('/loggjafarthing', 'GET');

        /** @var  $response \Zend\Http\PhpEnvironment\Response */
        $response = $this->getApplication()->getResponse();
        $range = $response->getHeaders()->get('Content-Range')->getFieldValue();

        $this->assertEquals($result, $range);
        $this->assertResponseStatusCode(206);
    }

    public function testOptionsList()
    {
        $this->dispatch('/loggjafarthing', 'OPTIONS');

        $headers = $this->getApplication()
            ->getResponse()
            ->getHeaders();

        $allowedMethods = array_map(function ($verb) {
            return trim($verb);
        }, explode(',', $headers->get('Allow')->getFieldValue()));

        $expectedMethods = ['OPTIONS', 'GET'];

        $this->assertResponseStatusCode(200);
        $this->assertEmpty(array_diff($expectedMethods, $allowedMethods));
        $this->assertEquals('*', $headers->get('Access-Control-Allow-Origin')->getFieldValue());
    }

    public function testPutListNotImplemented()
    {
        $this->dispatch('/loggjafarthing', 'PUT');
        $this->assertResponseStatusCode(405);
    }

    public function testPutSuccessful()
    {
        $serviceMock = \Mockery::mock('\Althingi\Service\Assembly')
            ->shouldReceive('create')
                ->andReturn(null)
                ->once()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);

        $this->dispatch('/loggjafarthing/144', 'PUT', [
            'from' => '2001-01-01',
            'to' => '2001-01-01',
        ]);
        $this->assertResponseStatusCode(201);
    }

    public function testPutParamMissing()
    {
        $serviceMock = \Mockery::mock('\Althingi\Service\Assembly')
            ->shouldReceive('create')
            ->andReturn(null)
            ->once()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);

        $this->dispatch('/loggjafarthing/144', 'PUT', [
            'to' => '2001-01-01',
        ]);
        $this->assertResponseStatusCode(400);
    }

    /**
     * @todo fixme
     */
    public function xtestPutResourceExits()
    {
        $serviceMock = \Mockery::mock('\Althingi\Service\Assembly')
            ->shouldReceive('create')
            ->andThrow('\PDOException', 'message', 23000)
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);

        $this->dispatch('/loggjafarthing/144', 'PUT', [
            'from' => '2001-01-01',
        ]);
        $this->assertResponseStatusCode(400);
    }

    public function testGet()
    {
        $serviceMock = \Mockery::mock('\Althingi\Service\Assembly')
            ->shouldReceive('get')
            ->andReturn(new \stdClass())
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);

        $this->dispatch('/loggjafarthing/144', 'GET');
        $this->assertResponseStatusCode(200);
    }

    public function testGetNotFound()
    {
        $serviceMock = \Mockery::mock('\Althingi\Service\Assembly')
            ->shouldReceive('get')
            ->andReturn(null)
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);

        $this->dispatch('/loggjafarthing/144', 'GET');
        $this->assertResponseStatusCode(404);
    }
}
