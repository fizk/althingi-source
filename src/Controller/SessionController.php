<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequest;
use Althingi\Form;
use Althingi\Injector\ServiceSessionAwareInterface;
use Althingi\Service\Session;
use Althingi\Router\{
    RestControllerTrait,
    RestControllerInterface,
    RouteInterface,
    RouterAwareInterface
};
use Althingi\Utils\{
    ErrorFormResponse,
    ErrorExceptionResponse
};
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};

/**
 * Class SessionController
 * @package Althingi\Controller
 * @todo PUT / PATCH
 */
class SessionController implements
    RestControllerInterface,
    ServiceSessionAwareInterface,
    RouterAwareInterface
{
    use RestControllerTrait;
    private RouteInterface $router;
    private Session $sessionService;

    /**
     * @output \Althingi\Model\Session
     * @200 Success
     * @404 Success
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $session = $this->sessionService->get(
            $request->getAttribute('session_id')
        );
        return $session
            ? new JsonResponse($session)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\Session[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $congressmanId = $request->getAttribute('congressman_id');

        $sessions = $this->sessionService->fetchByCongressman($congressmanId);

        return new JsonResponse($sessions, 206);
    }

    public function assemblyCongressmanAction(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $congressmanId = $request->getAttribute('congressman_id');
        $sessions = $this->sessionService->fetchByAssemblyAndCongressman($assemblyId, $congressmanId);
        return new JsonResponse($sessions, 206);
    }

    /**
     * Create a new Congressman session.
     *
     * @todo Session do not have IDs coming from althingi.is.
     *  They are created on this server. To be able to update
     *  these entries, the server has to provide the client with
     *  the URI created on the server. This method will try to
     *  create a resource and if the DB responds with a 23000 (ER_DUP_KEY)
     *  it will then try to find the server's ID and create an URI
     *  from that and pass it back in the HTTP's header Location as
     *  well as issuing a 409 response code. The client can then
     *  try to do a PATCH request with the URI provided.
     *
     *  If althingi.is will start to provide a session_id, then this will
     *  not be needed as the resource wil be stores via PUSH request.
     *
     *  To facilitate that, create a self::push() method and remove
     *  \Althingi\Service\Session::getIdentifier()
     *
     * @input \Althingi\Form\Session
     * @201 Created
     * @409 Conflict
     * @400 Invalid input
     */
    public function post(ServerRequest $request): ResponseInterface
    {
        $congressmanId = $request->getAttribute('congressman_id');
        $statusCode = 201;
        $sessionId = 0;

        $form = new Form\Session();
        $form->setData(array_merge($request->getParsedBody(), ['congressman_id' => $congressmanId]));

        if ($form->isValid()) {
            /** @var \Althingi\Model\Session */
            $sessionObject = $form->getObject();

            try {
                $sessionId = $this->sessionService->create($sessionObject);
                $statusCode = 201;
            } catch (\PDOException $e) {
                if ($e->errorInfo[1] === 1062) {
                    $sessionId = $this->sessionService->getIdentifier(
                        $sessionObject->getCongressmanId(),
                        $sessionObject->getFrom(),
                        $sessionObject->getType()
                    );
                    $statusCode = 409;
                } else {
                    return new ErrorExceptionResponse($e);
                }
            }

            return new EmptyResponse($statusCode, [
                'Location' => $this->router->assemble([
                    'congressman_id' => $congressmanId,
                    'session_id' => $sessionId
                ], ['name' => 'thingmenn/thingseta'])
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
        if (($session = $this->sessionService->get(
            $request->getAttribute('session_id')
        )) !== null) {
            $form = new Form\Session();
            $form->bind($session);
            $form->setData($request->getParsedBody());

            if ($form->isValid()) {
                $this->sessionService->update($form->getObject());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function setSessionService(Session $session): self
    {
        $this->sessionService = $session;
        return $this;
    }

    public function setRouter(RouteInterface $router): self
    {
        $this->router = $router;
        return $this;
    }
}
