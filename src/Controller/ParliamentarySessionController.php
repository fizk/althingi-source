<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequest;
use Althingi\Form;
use Althingi\Service\ParliamentarySession;
use Althingi\Injector\ServiceParliamentarySessionAwareInterface;
use Althingi\Utils\ErrorFormResponse;
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait
};
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};

class ParliamentarySessionController implements
    RestControllerInterface,
    ServiceParliamentarySessionAwareInterface
{
    use RestControllerTrait;

    private ParliamentarySession $parliamentarySessionService;

    /**
     * @output \Althingi\Model\ParliamentarySession
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $session = $this->parliamentarySessionService->get(
            $request->getAttribute('id'),
            $request->getAttribute('parliamentary_session_id')
        );

        return $session
            ? new JsonResponse($session, 200)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\ParliamentarySession[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $session = $this->parliamentarySessionService->fetchByAssembly(
            $request->getAttribute('id', null),
            0, // $range->getFrom(),
            $this->parliamentarySessionService->countByAssembly(
                $request->getAttribute('id', null)
            )
            //($range->getFrom()-$range->getTo())
        );
        return new JsonResponse($session, 206);
    }

    /**
     * @input \Althingi\Form\ParliamentarySession
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $form = new Form\ParliamentarySession([
            ...$request->getParsedBody(),
            'assembly_id' => $request->getAttribute('id'),
            'parliamentary_session_id' => $request->getAttribute('parliamentary_session_id'),
        ]);

        if ($form->isValid()) {
            $affectedRows = $this->parliamentarySessionService->save($form->getModel());
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return new EmptyResponse(404);
    }

    /**
     * @input \Althingi\Form\ParliamentarySession
     * @205 Updated
     * @400 Invalid input
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        if (
            ($session = $this->parliamentarySessionService->get(
                $request->getAttribute('id'),
                $request->getAttribute('parliamentary_session_id')
            )) != null
        ) {
            $form = new Form\ParliamentarySession([
                ...$session->toArray(),
                ...$request->getParsedBody(),
                'assembly_id' => $request->getAttribute('id'),
                'parliamentary_session_id' => $request->getAttribute('parliamentary_session_id'),
            ]);

            if ($form->isValid()) {
                $this->parliamentarySessionService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function setParliamentarySession(ParliamentarySession $parliamentarySession): static
    {
        $this->parliamentarySessionService = $parliamentarySession;
        return $this;
    }
}
