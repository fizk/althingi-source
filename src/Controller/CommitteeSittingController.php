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
use Althingi\Injector\ServiceCommitteeSittingAwareInterface;
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
 * Class CommitteeSittingController
 * @package Althingi\Controller
 */
class CommitteeSittingController implements
    RestControllerInterface,
    ServiceCommitteeSittingAwareInterface,
    RouterAwareInterface
{
    use RestControllerTrait;

    private RouteInterface $router;
    private Service\CommitteeSitting $committeeSittingService;

    /**
     * @output \Althingi\Model\CommitteeSitting
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $committeeSitting = $this->committeeSittingService->get(
            $request->getAttribute('committee_sitting_id')
        );
        return $committeeSitting
            ? new JsonResponse($committeeSitting)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\CommitteeSitting[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $congressmanId = $request->getAttribute('congressman_id');

        $sessions = $this->committeeSittingService->fetchByCongressman($congressmanId);

        return new JsonResponse($sessions, 206);
    }

    /**
     * Create a new Congressman session.
     *
     * @todo CommitteeSitting do not have IDs coming from althingi.is.
     *  They are created on this server. To be able to update
     *  these entries, the server has to provide the client with
     *  the URI created on the server. This method will try to
     *  create a resource and if the DB responds with a 23000 (ER_DUP_KEY)
     *  it will then try to find the server's ID and create an URI
     *  from that and pass it back in the HTTP's header Location as
     *  well as issuing a 409 response code. The client can then
     *  try to do a PATCH request with the URI provided.
     *
     *  If althingi.is will start to provide a CommitteeSittingIDs, then this will
     *  not be needed as the resource wil be stores via PUSH request.
     *
     *  To facilitate that, create a self::push() method and remove
     *  \Althingi\Service\CommitteeSitting::getIdentifier()
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
        $committeeSittingId = 0;

        $form = new Form\CommitteeSitting([
            ...$request->getParsedBody(),
            'congressman_id' => $congressmanId,
        ]);

        if ($form->isValid()) {
            /** @var \Althingi\Model\CommitteeSitting */
            $committeeSitting = $form->getModel();

            try {
                $committeeSittingId = $this->committeeSittingService->create($committeeSitting);
                $statusCode = 201;
            } catch (\PDOException $e) {
                if ($e->errorInfo[1] === 1062) {
                    $committeeSittingId = $this->committeeSittingService->getIdentifier(
                        $committeeSitting->getCongressmanId(),
                        $committeeSitting->getCommitteeId(),
                        $committeeSitting->getAssemblyId(),
                        $committeeSitting->getFrom()
                    );
                    $statusCode = 409;
                } else {
                    return new ErrorExceptionResponse($e);
                }
            }

            return new EmptyResponse($statusCode, [
                'Location' => $this->router->assemble([
                    'congressman_id' => $congressmanId,
                    'committee_sitting_id' => $committeeSittingId
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
            ($committeeSitting = $this->committeeSittingService->get(
                $request->getAttribute('committee_sitting_id')
            )) != null
        ) {
            $form = new Form\CommitteeSitting([
                ...$committeeSitting->toArray(),
                ...$request->getParsedBody(),
                'committee_sitting_id' => $request->getAttribute('committee_sitting_id'),
            ]);

            if ($form->isValid()) {
                $this->committeeSittingService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function setCommitteeSitting(Service\CommitteeSitting $committeeSitting): static
    {
        $this->committeeSittingService = $committeeSitting;
        return $this;
    }

    public function setRouter(RouteInterface $router): static
    {
        $this->router = $router;
        return $this;
    }
}
