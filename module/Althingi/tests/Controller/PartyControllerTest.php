<?php

namespace AlthingiTest\Controller;

use Althingi\Service\Party;
use AlthingiTest\ServiceHelper;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class PartyControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\PartyController
 *
 * @covers \Althingi\Controller\PartyController::setPartyService
 */
class PartyControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Party::class,
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
        $expectedData = (new \Althingi\Model\Party())
            ->setPartyId(100)
            ->setName('n1')
            ->setAbbrShort('p1')
            ->setAbbrLong('p2')
            ->setColor('blue');

        $this->getMockService(Party::class)
            ->shouldReceive('save')
            ->with(\Mockery::on(function ($actualData) use ($expectedData) {
                return $expectedData == $actualData;
            }))
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/thingflokkar/100', 'PUT', [
            'name' => 'n1',
            'abbr_short' => 'p1',
            'abbr_long' => 'p2',
            'color' => 'blue'
        ]);

        $this->assertControllerClass('PartyController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $expectedData = (new \Althingi\Model\Party())
            ->setPartyId(100)
            ->setName('n1')
            ->setAbbrShort('p1')
            ->setAbbrLong('p2')
            ->setColor('blue');

        $this->getMockService(Party::class)
            ->shouldReceive('get')
            ->with(100)
            ->andReturn((new \Althingi\Model\Party())
                ->setPartyId(100)
                ->setName('n1')
                ->setAbbrShort('p1')
                ->setAbbrLong('p2')
                ->setColor('green'))
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->with(\Mockery::on(function ($actualData) use ($expectedData) {
                return $expectedData == $actualData;
            }))
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/thingflokkar/100', 'PATCH', [
            'color' => 'blue'
        ]);

        $this->assertControllerClass('PartyController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }
}
