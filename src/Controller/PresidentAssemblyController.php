<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Althingi\Injector\ServiceCongressmanAwareInterface;
use Althingi\Service\Congressman;
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait
};

class PresidentAssemblyController implements
    RestControllerInterface,
    ServiceCongressmanAwareInterface
{
    use RestControllerTrait;

    private Congressman $congressmanService;

    /**
     * @output \Althingi\Model\PresidentPartyProperties
     * @200 Success
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $presidentId = $request->getAttribute('president_id');
        $residents = $this->congressmanService->getPresidentByAssembly($assemblyId, $presidentId);

        return new JsonResponse($residents, 206);
    }

    /**
     * @output \Althingi\Model\PresidentPartyProperties[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $residents = $this->congressmanService->fetchPresidentsByAssembly($assemblyId);

        return new JsonResponse($residents, 206);
    }

    public function setCongressmanService(Congressman $congressman): static
    {
        $this->congressmanService = $congressman;
        return $this;
    }
}
