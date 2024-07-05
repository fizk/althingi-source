<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};
use Althingi\Injector\ServiceMinistryAwareInterface;
use Althingi\Service;
use Althingi\Router\{
    RestControllerTrait,
    RestControllerInterface
};

/**
 * Class MinisterController
 * @package Althingi\Controller
 */
class MinisterController implements
    RestControllerInterface,
    ServiceMinistryAwareInterface
{
    use RestControllerTrait;

    private Service\Ministry $ministryService;

    /**
     * @output \Althingi\Model\Ministry
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $ministry = $this->ministryService->getByCongressmanAssembly(
            $request->getAttribute('id'),
            $request->getAttribute('congressman_id'),
            $request->getAttribute('ministry_id')
        );

        return $ministry
            ? new JsonResponse($ministry)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\Ministry[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $ministries = $this->ministryService->fetchByCongressmanAssembly(
            $request->getAttribute('id'),
            $request->getAttribute('congressman_id')
        );

        return new JsonResponse($ministries, 206);
    }

    public function setMinistryService(Service\Ministry $ministry): static
    {
        $this->ministryService = $ministry;
        return $this;
    }
}
