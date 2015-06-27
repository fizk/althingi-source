<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 6/06/15
 * Time: 10:28 PM
 */

namespace Althingi\Controller;

use Zend\Stdlib\Parameters;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class AssemblyRangeControllerTest extends AbstractHttpControllerTestCase
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
        $this->dispatch('/api/loggjafarthing', 'GET');

        /** @var  $response \Zend\Http\PhpEnvironment\Response */
        $response = $this->getApplication()->getResponse();
        $range = $response->getHeaders()->get('Content-Range')->getFieldValue();

        $this->assertEquals($result, $range);
        $this->assertResponseStatusCode(206);
    }
}
