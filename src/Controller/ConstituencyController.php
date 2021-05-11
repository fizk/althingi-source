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
use Althingi\Injector\ServiceConstituencyAwareInterface;
use Althingi\Utils\ErrorFormResponse;
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait
};

class ConstituencyController implements
    RestControllerInterface,
    ServiceConstituencyAwareInterface
{
    use RestControllerTrait;
    private Service\Constituency $constituencyService;

    /**
     * @output \Althingi\Model\Constituency
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $constituency = $this->constituencyService->get(
            $request->getAttribute('id')
        );
        return $constituency
            ? new JsonResponse($constituency)
            : new EmptyResponse(404);
    }

    /**
     * @input Althingi\Form\Constituency
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $form = new Form\Constituency();
        $form->setData(array_merge($request->getParsedBody(), ['constituency_id' => $request->getAttribute('id')]));
        if ($form->isValid()) {
            $affectedRows = $this->constituencyService->save($form->getObject());
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return new EmptyResponse(404);
    }

    /**
     * @input Althingi\Form\Constituency
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        if (($constituency = $this->constituencyService->get(
            $request->getAttribute('id')
        )) !== null) {
            $form = new Form\Constituency();
            $form->bind($constituency);
            $form->setData($request->getParsedBody());

            if ($form->isValid()) {
                $this->constituencyService->update($form->getObject());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function setConstituencyService(Service\Constituency $constituency): self
    {
        $this->constituencyService = $constituency;
        return $this;
    }
}
