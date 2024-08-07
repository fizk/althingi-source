<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Althingi\Injector\ServiceCommitteeAwareInterface;
use Althingi\Service\Committee;
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait
};
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};

class AssemblyCommitteeController implements
    RestControllerInterface,
    ServiceCommitteeAwareInterface
{
    use RestControllerTrait;

    private Committee $committeeService;

    /**
     * Get one committee which is/was active during an assebly.
     *
     * @output \Althingi\Model\Committee
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $committee = $this->committeeService->get(
            $request->getAttribute('committee_id')
        );

        return $committee
            ? new JsonResponse($committee, 200)
            : new EmptyResponse(404);
    }

    /**
     * Get all active committies for a given assembly.
     *
     * @output \Althingi\Model\Committee[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $committees = $this->committeeService->fetchByAssembly(
            $request->getAttribute('id')
        );

        return new JsonResponse($committees, 206);
    }

    public function setCommitteeService(Committee $committee): static
    {
        $this->committeeService = $committee;
        return $this;
    }
}
