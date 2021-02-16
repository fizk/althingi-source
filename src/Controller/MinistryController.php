<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};
use Althingi\Service;
use Althingi\Form;
use Althingi\Injector\ServiceMinistryAwareInterface;
use Althingi\Utils\ErrorFormResponse;
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait
};

class MinistryController implements
    RestControllerInterface,
    ServiceMinistryAwareInterface
{
    use RestControllerTrait;
    private Service\Ministry $ministryService;

    /**
     * @output \Althingi\Model\Ministry
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $ministry = $this->ministryService->get(
            $request->getAttribute('id')
        );
        return $ministry
            ? new JsonResponse($ministry)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\Ministry[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $ministries = $this->ministryService->fetchAll();

        return new JsonResponse($ministries, 206);
    }

    /**
     * @200 Success
     */
    public function optionsList(ServerRequest $request): ResponseInterface
    {
        return (new EmptyResponse(200, [
            'Allow' => 'GET, OPTIONS'
        ]));
    }

    /**
     * @200 Success
     */
    public function options(ServerRequest $request): ResponseInterface
    {
        return (new EmptyResponse(200, [
            'Allow' => 'GET, OPTIONS, PUT, PATCH, DELETE'
        ]));
    }

    /**
     * @input \Althingi\Form\Ministry
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $form = new Form\Ministry();
        $form->bindValues(array_merge($request->getParsedBody(), ['ministry_id' => $request->getAttribute('id')]));

        if ($form->isValid()) {
            $object = $form->getObject();
            $affectedRows = $this->ministryService->save($object);
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * @input \Althingi\Form\Ministry
     * @205 Update
     * @400 Invalid input
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        if (($assembly = $this->ministryService->get(
            $request->getAttribute('id')
        )) != null) {
            $form = new Form\Ministry();
            $form->bind($assembly);
            $form->setData($request->getParsedBody());

            if ($form->isValid()) {
                $this->ministryService->update($form->getData());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function setMinistryService(Service\Ministry $ministry): self
    {
        $this->ministryService = $ministry;
        return $this;
    }
}
