<?php

namespace Althingi\Controller;

use Althingi\{Model, Service};
use Althingi\Controller\PresidentController;
use Althingi\ServiceHelper;
use DateTime;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(PresidentController::class)]
#[CoversMethod(PresidentController::class, 'setPresidentService')]
#[CoversMethod(PresidentController::class, 'get')]
#[CoversMethod(PresidentController::class, 'getList')]
#[CoversMethod(PresidentController::class, 'patch')]
#[CoversMethod(PresidentController::class, 'post')]
class PresidentControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Party::class,
            Service\President::class,
            Service\Congressman::class,
        ]);
    }

    #[After]
    public function down(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function getSuccess()
    {
        $this->getMockService(Service\President::class)
            ->shouldReceive('get')
            ->with(1)
            ->andReturn(
                (new Model\President())
                    ->setPresidentId(1)
                    ->setCongressmanId(2)
                    ->setAssemblyId(4)
                    ->setFrom(new DateTime())
                    ->setTitle('title')
            )
            ->once()
            ->getMock();


        $this->dispatch('/forsetar/1');

        $this->assertControllerName(PresidentController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getNotFound()
    {
        $this->getMockService(Service\President::class)
            ->shouldReceive('get')
            ->with(1)
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/forsetar/1');

        $this->assertControllerName(PresidentController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function getList()
    {
        $this->getMockService(Service\President::class)
            ->shouldReceive('fetch')
            ->withNoArgs()
            ->andReturn([
                (new Model\President())
                    ->setPresidentId(1)
                    ->setCongressmanId(2)
                    ->setAssemblyId(4)
                    ->setFrom(new DateTime())
                    ->setTitle('title')
            ])
            ->once()
            ->getMock();

        $this->dispatch('/forsetar');

        $this->assertControllerName(PresidentController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function postSuccess()
    {
        $autoGeneratedPresidentId = 101;

        $expectedData = (new Model\President())
            ->setPresidentId(0)
            ->setAssemblyId(1)
            ->setCongressmanId(100)
            ->setTitle('some title')
            ->setAbbr('abbr')
            ->setFrom(new \DateTime('2001-01-01'));

        $this->getMockService(Service\President::class)
            ->shouldReceive('create')
            ->with(\Mockery::on(function ($actualData) use ($expectedData) {
                return $actualData == $expectedData;
            }))
            ->andReturn($autoGeneratedPresidentId)
            ->once()
            ->getMock();

        $this->dispatch('/forsetar', 'POST', [
            'assembly_id' => 1,
            'congressman_id' => 100,
            'title' => 'some title',
            'abbr' => 'abbr',
            'from' => '2001-01-01',
        ]);

        $this->assertControllerName(PresidentController::class);
        $this->assertActionName('post');
        $this->assertResponseStatusCode(201);

        $this->assertResponseHeaderContains('Location', "/forsetar/{$autoGeneratedPresidentId}");
    }

    #[Test]
    public function postAlreadyExists()
    {
        $exception = new \PDOException();
        $exception->errorInfo = ['', 1062, ''];

        $autoGeneratedPresidentId = 1234;

        $expectedData = (new Model\PresidentCongressman())
            ->setPresidentId($autoGeneratedPresidentId)
            ->setAssemblyId(1)
            ->setCongressmanId(100)
            ->setTitle('some title')
            ->setAbbr('abbr')
            ->setFrom(new \DateTime('2001-01-01'));

        $this->getMockService(Service\President::class)
            ->shouldReceive('create')
            ->andThrow($exception)
            ->once()
            ->getMock()

            ->shouldReceive('getByUnique')
            ->once()
            ->andReturn($expectedData)
            ->getMock()
        ;

        $this->dispatch('/forsetar', 'POST', [
            'assembly_id' => 1,
            'congressman_id' => 100,
            'title' => 'some title',
            'abbr' => 'abbr',
            'from' => '2001-01-01',
        ]);

        $this->assertControllerName(PresidentController::class);
        $this->assertActionName('post');
        $this->assertResponseStatusCode(409);

        $this->assertResponseHeaderContains('Location', "/forsetar/{$autoGeneratedPresidentId}");
    }

    #[Test]
    public function postInvalid()
    {
        $this->getMockService(Service\President::class)
            ->shouldReceive('create')
            ->never()
            ->getMock();

        $this->dispatch('/forsetar', 'POST', [
            'assembly_id' => 1,
            'congressman_id' => 100,
            'title' => 'some title',
            'abbr' => 'abbr',
            'from' => 'invalid-data',
        ]);

        $this->assertControllerName(PresidentController::class);
        $this->assertActionName('post');
        $this->assertResponseStatusCode(400);
    }

    #[Test]
    public function patch()
    {
        $autoGeneratedPresidentId = 101;

        $expectedData = (new Model\President())
            ->setPresidentId(0)
            ->setAssemblyId(1)
            ->setCongressmanId(100)
            ->setTitle('another title')
            ->setAbbr('abbr')
            ->setFrom(new \DateTime('2001-01-01'));

        $this->getMockService(Service\President::class)
            ->shouldReceive('get')
            ->with(200)
            ->once()
            ->andReturn((new Model\President())
                ->setPresidentId(0)
                ->setAssemblyId(1)
                ->setCongressmanId(100)
                ->setTitle('old title')
                ->setAbbr('abbr')
                ->setFrom(new \DateTime('2001-01-01')))
            ->getMock()

            ->shouldReceive('update')
            ->with(\Mockery::on(function ($actualData) use ($expectedData) {
                return $actualData == $expectedData;
            }))
            ->andReturn($autoGeneratedPresidentId)
            ->once()
            ->getMock();

        $this->dispatch('/forsetar/200', 'PATCH', [
            'title' => 'another title',
        ]);

        $this->assertControllerName(PresidentController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    #[Test]
    public function patchInvalidParams()
    {
        $this->getMockService(Service\President::class)
            ->shouldReceive('get')
            ->with(200)
            ->once()
            ->andReturn((new Model\President())
                ->setPresidentId(0)
                ->setAssemblyId(1)
                ->setCongressmanId(100)
                ->setTitle('old title')
                ->setAbbr('abbr')
                ->setFrom(new \DateTime('2001-01-01')))
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/forsetar/200', 'PATCH', [
            'from' => 'invalid date',
        ]);

        $this->assertControllerName(PresidentController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    #[Test]
    public function patchNotFound()
    {
        $this->getMockService(Service\President::class)
            ->shouldReceive('get')
            ->with(200)
            ->once()
            ->andReturn(null)
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/forsetar/200', 'PATCH', [
            'title' => 'another title',
        ]);

        $this->assertControllerName(PresidentController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }
}
