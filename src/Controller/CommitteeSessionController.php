<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\{
    EmptyResponse,
    JsonResponse
};
use Althingi\Form;
use Althingi\Service;
use Althingi\Injector\ServiceCommitteeSessionAwareInterface;
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait,
    RouteInterface,
    RouterAwareInterface
};
use Althingi\Utils\{
    ErrorFormResponse,
    ErrorExceptionResponse
};

/**
 * Class CommitteeSessionController
 * @package Althingi\Controller
 */
class CommitteeSessionController implements
    RestControllerInterface,
    ServiceCommitteeSessionAwareInterface,
    RouterAwareInterface
{
    use RestControllerTrait;

    private RouteInterface $router;
    private Service\CommitteeSession $committeeSessionService;

    /**
     * @output \Althingi\Model\CommitteeSession
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $session = $this->committeeSessionService->get(
            $request->getAttribute('committee_session_id')
        );

        return $session
            ? new JsonResponse($session)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\CommitteeSession[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $sessions = $this->committeeSessionService->fetchByCongressman(
            $request->getAttribute('congressman_id')
        );

        return new JsonResponse($sessions, 206);
    }

    /**
     * Create a new Congressman session.
     *
     * @todo CommitteeSession do not have IDs coming from althingi.is.
     *  They are created on this server. To be able to update
     *  these entries, the server has to provide the client with
     *  the URI created on the server. This method will try to
     *  create a resource and if the DB responds with a 23000 (ER_DUP_KEY)
     *  it will then try to find the server's ID and create an URI
     *  from that and pass it back in the HTTP's header Location as
     *  well as issuing a 409 response code. The client can then
     *  try to do a PATCH request with the URI provided.
     *
     *  If althingi.is will start to provide a CommitteeSessionIDs, then this will
     *  not be needed as the resource wil be stores via PUSH request.
     *
     *  To facilitate that, create a self::push() method and remove
     *  \Althingi\Service\CommitteeSession::getIdentifier()
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
        $committeeSessionId = 0;

        $form = new Form\CommitteeSession([
            ...$request->getParsedBody(),
            'congressman_id' => $congressmanId,
        ]);

        if ($form->isValid()) {
            /** @var \Althingi\Model\CommitteeSession */
            $committeeSession = $form->getModel();

            try {
                $committeeSessionId = $this->committeeSessionService->create($committeeSession);
                $statusCode = 201;
            } catch (\PDOException $e) {
                if ($e->errorInfo[1] === 1062) {
                    $committeeSessionId = $this->committeeSessionService->getIdentifier(
                        $committeeSession->getCongressmanId(),
                        $committeeSession->getCommitteeId(),
                        $committeeSession->getAssemblyId(),
                        $committeeSession->getFrom()
                    );
                    $statusCode = 409;
                } else {
                    return new ErrorExceptionResponse($e);
                }
            }

            return new EmptyResponse($statusCode, [
                'Location' => $this->router->assemble([
                    'congressman_id' => $congressmanId,
                    'committee_session_id' => $committeeSessionId
                ], ['name' => 'thingmenn/nefndaseta'])
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
            ($session = $this->committeeSessionService->get(
                $request->getAttribute('committee_session_id')
            )) != null
        ) {
            $form = new Form\CommitteeSession([
                ...$session->toArray(),
                ...$request->getParsedBody(),
                'committee_session_id' => $request->getAttribute('committee_session_id'),
            ]);

            if ($form->isValid()) {
                $this->committeeSessionService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function setCommitteeSession(Service\CommitteeSession $committeeSession): static
    {
        $this->committeeSessionService = $committeeSession;
        return $this;
    }

    public function setRouter(RouteInterface $router): static
    {
        $this->router = $router;
        return $this;
    }
}
