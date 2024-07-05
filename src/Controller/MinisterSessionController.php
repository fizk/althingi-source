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
use Althingi\Injector\ServiceMinisterSessionAwareInterface;
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
 * Class MinisterSessionController
 * @package Althingi\Controller
 */
class MinisterSessionController implements
    RestControllerInterface,
    ServiceMinisterSessionAwareInterface,
    RouterAwareInterface
{
    use RestControllerTrait;

    private RouteInterface $router;
    private Service\MinisterSession $ministerSessionService;

    /**
     * @output \Althingi\Model\CommitteeSession
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $ministerSession = $this->ministerSessionService->get(
            $request->getAttribute('ministry_sitting_id')
        );

        return $ministerSession
            ? new JsonResponse($ministerSession)
            : new EmptyResponse(404);
    }

    /**
     * Create a new Congressman session.
     *
     * @todo MinisterSession do not have IDs coming from althingi.is.
     *  They are created on this server. To be able to update
     *  these entries, the server has to provide the client with
     *  the URI created on the server. This method will try to
     *  create a resource and if the DB responds with a 23000 (ER_DUP_KEY)
     *  it will then try to find the server's ID and create an URI
     *  from that and pass it back in the HTTP's header Location as
     *  well as issuing a 409 response code. The client can then
     *  try to do a PATCH request with the URI provided.
     *
     *  If althingi.is will start to provide a MinisterSessionIDs, then this will
     *  not be needed as the resource wil be stores via PUSH request.
     *
     *  To facilitate that, create a self::push() method and remove
     *  \Althingi\Service\MinisterSession::getIdentifier()
     *
     * @input \Althingi\Form\CommitteeSession
     * @201 Created
     * @409 Conflict
     * @400 Invalid input
     */
    public function post(ServerRequest $request): ResponseInterface
    {
        $congressmanId = $request->getAttribute('congressman_id');
        $statusCode = 201;
        $ministerSessionId = 0;

        $form = new Form\MinisterSession([
            ...$request->getParsedBody(),
            'congressman_id' => $congressmanId,
        ]);

        if ($form->isValid()) {
            $ministerSession = $form->getModel();

            try {
                $ministerSessionId = $this->ministerSessionService->create($ministerSession);
                $statusCode = 201;
            } catch (\PDOException $e) {
                if ($e->errorInfo[1] == 1062) {
                    $ministerSessionId = $this->ministerSessionService->getIdentifier(
                        $ministerSession->getAssemblyId(),
                        $ministerSession->getMinistryId(),
                        $ministerSession->getCongressmanId(),
                        $ministerSession->getFrom()
                    );
                    $statusCode = 409;
                } else {
                    return new ErrorExceptionResponse($e);
                }
            }

            return new EmptyResponse($statusCode, [
                'Location' => $this->router->assemble([
                    'congressman_id' => $congressmanId,
                    'ministry_sitting_id' => $ministerSessionId
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
            ($ministerSession = $this->ministerSessionService->get(
                $request->getAttribute('ministry_sitting_id')
            )) != null
        ) {
            $form = new Form\MinisterSession([
                ...$ministerSession->toArray(),
                ...$request->getParsedBody(),
                'ministry_sitting_id' => $request->getAttribute('ministry_sitting_id'),
            ]);

            if ($form->isValid()) {
                $this->ministerSessionService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    /**
     * @output \Althingi\MinisterSessionProperties[]
     * @206 Success
     */
    public function assemblySessionsAction(ServerRequest $request): ResponseInterface
    {
        $sittings = $this->ministerSessionService->fetchByCongressmanAssembly(
            $request->getAttribute('id', 0),
            $request->getAttribute('congressman_id', 0)
        );

        return new JsonResponse($sittings, 206);
    }

    public function setMinisterSessionService(Service\MinisterSession $ministerSession): static
    {
        $this->ministerSessionService = $ministerSession;
        return $this;
    }

    public function setRouter(RouteInterface $router): static
    {
        $this->router = $router;
        return $this;
    }
}
