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
use Althingi\Injector\{
    ServiceCabinetAwareInterface,
    ServiceAssemblyAwareInterface,
    ServiceInflationAwareInterface
};
use Althingi\Utils\ErrorFormResponse;
use Althingi\Router\{
    RestControllerTrait,
    RestControllerInterface
};
use DateTime;

class InflationController implements
    RestControllerInterface,
    ServiceInflationAwareInterface,
    ServiceCabinetAwareInterface,
    ServiceAssemblyAwareInterface
{
    use RestControllerTrait;
    private Service\Inflation $inflationService;
    private Service\Cabinet $cabinetService;
    private Service\Assembly $assemblyService;

    /**
     * @output \Althingi\Model\Inflation
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $inflation = $this->inflationService->get(
            $request->getAttribute('id')
        );
        return $inflation
            ? new JsonResponse($inflation)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\Inflation[]
     * @query fra
     * @query til
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $from = $request->getQueryParams()['fra'] ?? null;
        $to = $request->getQueryParams()['til'] ?? null;
        $assembly = $request->getQueryParams()['loggjafarthing'] ?? null;


        if ($assembly) {
            $cabinet = $this->cabinetService->fetchByAssembly($assembly);
            if (count($cabinet) > 0) {
                $inflationCollection = $this->inflationService->fetchAll(
                    $cabinet[0]->getFrom(),
                    $cabinet[0]->getTo()
                );
                return new JsonResponse($inflationCollection, 206);
            } else {
                return new JsonResponse([], 206);
            }
        } else {
            $inflationCollection = $this->inflationService->fetchAll(
                $from ? new DateTime($from) : null,
                $to ? new DateTime($to) : null
            );
            return new JsonResponse($inflationCollection, 206);
        }
    }

    /**
     * @output \Althingi\Model\Inflation[]
     * @206
     */
    public function fetchAssemblyAction(ServerRequest $request): ResponseInterface
    {
        $assembly = $this->assemblyService->get($request->getAttribute('id'));
        $cabinet = $this->cabinetService->fetchByAssembly($assembly->getAssemblyId());

        if (count($cabinet) > 0) {
            $from = $assembly->getFrom() < $cabinet[0]->getFrom() ? $assembly->getFrom() : $cabinet[0]->getFrom();
            $to = $assembly->getTo() > $cabinet[0]->getTo() ? $assembly->getTo() : $cabinet[0]->getTo();

            $inflationCollection = $this->inflationService->fetchAll($from, $to);
            return new JsonResponse($inflationCollection, 206);
        } else {
            return new JsonResponse([], 206);
        }
    }

    /**
     * @input \Althingi\Form\Committee
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {

        $form = new Form\Inflation([
            ...$request->getParsedBody(),
            'id' => $request->getAttribute('id')
        ]);

        if ($form->isValid()) {
            $affectedRows = $this->inflationService->save($form->getModel());
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
        if (($committee = $this->inflationService->get(
            $request->getAttribute('id')
        )) != null) {
            $form = new Form\Inflation([
                ...(new \Althingi\Hydrator\Inflation())->extract($committee),
                ...$request->getParsedBody(),
                'id' => $request->getAttribute('id'),
            ]);

            if ($form->isValid()) {
                $this->inflationService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function setInflationService(Service\Inflation $inflation): self
    {
        $this->inflationService = $inflation;
        return $this;
    }

    public function setCabinetService(Service\Cabinet $cabinet): self
    {
        $this->cabinetService = $cabinet;
        return $this;
    }

    public function setAssemblyService(Service\Assembly $assembly): self
    {
        $this->assemblyService = $assembly;
        return $this;
    }
}
