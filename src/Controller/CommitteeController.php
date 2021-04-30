<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequest;
use Althingi\Form;
use Althingi\Service;
use Althingi\Injector\ServiceCommitteeAwareInterface;
use Althingi\Utils\ErrorFormResponse;
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait
};
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};

class CommitteeController implements
    RestControllerInterface,
    ServiceCommitteeAwareInterface
{
    use RestControllerTrait;
    private Service\Committee $committeeService;

    /**
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
     * @output \Althingi\Model\Committee[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $committees = $this->committeeService->fetchAll();

        return new JsonResponse($committees, 206);
    }

    /**
     * @input \Althingi\Form\Committee
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $committeeId = $request->getAttribute('committee_id');

        $form = new Form\Committee();
        $form->bindValues(array_merge($request->getParsedBody(), [
            'assembly_id' => $assemblyId,
            'committee_id' => $committeeId
        ]));

        if ($form->isValid()) {
            $affectedRows = $this->committeeService->save($form->getObject());
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * @input \Althingi\Form\Committee
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        if (($committee = $this->committeeService->get($request->getAttribute('committee_id'))) != null) {
            $form = new Form\Committee();
            $form->bind($committee);
            $form->setData($request->getParsedBody());

            if ($form->isValid()) {
                $this->committeeService->update($form->getData());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    /**
     * @200 Success
     */
    public function optionsList(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(200, ['Allow' =>'GET, OPTIONS']);
    }

    /**
     * @200 Success
     */
    public function options(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(200, ['Allow' => 'GET, OPTIONS, PUT, PATCH']);
    }

    public function setCommitteeService(Service\Committee $committee): self
    {
        $this->committeeService = $committee;
        return $this;
    }
}
