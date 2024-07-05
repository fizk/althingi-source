<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};
use Althingi\Form;
use Althingi\Service;
use Althingi\Injector\ServiceMinisterSittingAwareInterface;
use Althingi\Utils\{
    ErrorFormResponse,
    ErrorExceptionResponse
};
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait,
    RouteInterface,
    RouterAwareInterface
};

/**
 * Class CommitteeSittingController
 * @package Althingi\Controller
 */
class MinisterSittingController implements
    RestControllerInterface,
    ServiceMinisterSittingAwareInterface,
    RouterAwareInterface
{
    use RestControllerTrait;

    private RouteInterface $router;
    private Service\MinisterSitting $ministerSittingService;

    /**
     * @output \Althingi\Model\CommitteeSitting
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $ministerSitting = $this->ministerSittingService->get(
            $request->getAttribute('ministry_sitting_id')
        );

        return $ministerSitting
            ? new JsonResponse($ministerSitting)
            : new EmptyResponse(404);
    }

    /**
     * Create a new Congressman session.
     *
     * @todo MinisterSitting do not have IDs coming from althingi.is.
     *  They are created on this server. To be able to update
     *  these entries, the server has to provide the client with
     *  the URI created on the server. This method will try to
     *  create a resource and if the DB responds with a 23000 (ER_DUP_KEY)
     *  it will then try to find the server's ID and create an URI
     *  from that and pass it back in the HTTP's header Location as
     *  well as issuing a 409 response code. The client can then
     *  try to do a PATCH request with the URI provided.
     *
     *  If althingi.is will start to provide a MinisterSittingIDs, then this will
     *  not be needed as the resource wil be stores via PUSH request.
     *
     *  To facilitate that, create a self::push() method and remove
     *  \Althingi\Service\MinisterSitting::getIdentifier()
     *
     * @input \Althingi\Form\CommitteeSitting
     * @201 Created
     * @409 Conflict
     * @400 Invalid input
     */
    public function post(ServerRequest $request): ResponseInterface
    {
        $congressmanId = $request->getAttribute('congressman_id');
        $statusCode = 201;
        $ministerSittingId = 0;

        $form = new Form\MinisterSitting([
            ...$request->getParsedBody(),
            'congressman_id' => $congressmanId,
        ]);

        if ($form->isValid()) {
            $ministerSitting = $form->getModel();

            try {
                $ministerSittingId = $this->ministerSittingService->create($ministerSitting);
                $statusCode = 201;
            } catch (\PDOException $e) {
                if ($e->errorInfo[1] == 1062) {
                    $ministerSittingId = $this->ministerSittingService->getIdentifier(
                        $ministerSitting->getAssemblyId(),
                        $ministerSitting->getMinistryId(),
                        $ministerSitting->getCongressmanId(),
                        $ministerSitting->getFrom()
                    );
                    $statusCode = 409;
                } else {
                    return new ErrorExceptionResponse($e);
                }
            }

            return new EmptyResponse($statusCode, [
                'Location' => $this->router->assemble([
                    'congressman_id' => $congressmanId,
                    'ministry_sitting_id' => $ministerSittingId
                ], ['name' => 'thingmenn/radherraseta'])
            ]);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * @input \Althingi\Form\Session
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        if (
            ($ministerSitting = $this->ministerSittingService->get(
                $request->getAttribute('ministry_sitting_id')
            )) != null
        ) {
            $form = new Form\MinisterSitting([
                ...$ministerSitting->toArray(),
                ...$request->getParsedBody(),
                'ministry_sitting_id' => $request->getAttribute('ministry_sitting_id'),
            ]);

            if ($form->isValid()) {
                $this->ministerSittingService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    /**
     * @output \Althingi\MinisterSittingProperties[]
     * @206 Success
     */
    public function assemblySessionsAction(ServerRequest $request): ResponseInterface
    {
        $sittings = $this->ministerSittingService->fetchByCongressmanAssembly(
            $request->getAttribute('id', 0),
            $request->getAttribute('congressman_id', 0)
        );

        return new JsonResponse($sittings, 206);
    }

    public function setMinisterSittingService(Service\MinisterSitting $ministerSitting): static
    {
        $this->ministerSittingService = $ministerSitting;
        return $this;
    }

    public function setRouter(RouteInterface $router): static
    {
        $this->router = $router;
        return $this;
    }
}
