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
use Althingi\Injector\ServicePartyAwareInterface;
use Althingi\Utils\ErrorFormResponse;
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait
};
use DateTime;

class PartyController implements
    RestControllerInterface,
    ServicePartyAwareInterface
{
    use RestControllerTrait;
    private Service\Party $partyService;

    /**
     * @output \Althingi\Model\Party
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $party = $this->partyService->get(
            $request->getAttribute('id')
        );
        return $party
            ? new JsonResponse($party)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\Party[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $parties = $this->partyService->fetch();
        return new JsonResponse($parties);
    }

    /**
     * @output \Althingi\Model\Party[]
     * @200 Success
     */
    public function getByCongressmanAction(ServerRequest $request): ResponseInterface
    {
        $params = $request->getQueryParams();
        if (array_key_exists('dags', $params)) {
            $parties = $this->partyService->getByCongressman(
                $request->getAttribute('congressman_id'),
                new DateTime($params['dags'])
            );
            return new JsonResponse([$parties]);
        } else {
            return new JsonResponse([]);
        }
    }

    /**
     * @input \Althingi\Form\Party
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $form = new Form\Party();
        $form->setData(array_merge($request->getParsedBody(), ['party_id' => $request->getAttribute('id')]));
        if ($form->isValid()) {
            $affectedRow = $this->partyService->save($form->getObject());
            return new EmptyResponse($affectedRow === 1 ? 201 : 205);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * @input \Althingi\Form\Party
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        if (($party = $this->partyService->get(
            $request->getAttribute('id')
        )) !== null) {
            $form = new Form\Party();
            $form->bind($party);
            $form->setData($request->getParsedBody());

            if ($form->isValid()) {
                $this->partyService->update($form->getObject());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }


    public function setPartyService(Service\Party $party): self
    {
        $this->partyService = $party;
        return $this;
    }
}
