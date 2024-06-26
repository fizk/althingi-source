<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Althingi\Form;
use Althingi\Utils\{ErrorFormResponse};
use Althingi\Service\{
    Assembly,
    Cabinet
};
use Althingi\Injector\{
    ServiceAssemblyAwareInterface,
    ServiceCabinetAwareInterface
};
use Althingi\Router\{
    RestControllerTrait,
    RestControllerInterface
};
use DateTime;

class CabinetController implements
    RestControllerInterface,
    ServiceCabinetAwareInterface,
    ServiceAssemblyAwareInterface
{
    use RestControllerTrait;

    private Cabinet $cabinetService;
    private Assembly $assemblyService;

    /**
     * @output \Althingi\Model\CabinetAndAssemblies
     * @200 Success
     * @404 Not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $cabinet = $this->cabinetService->get($request->getAttribute('id'));

        return $cabinet
            ? new JsonResponse($cabinet, 200)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\Cabinet[]
     * @query fra
     * @query til
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $from = $request->getQueryParams('fra');
        $to = $request->getQueryParams('til');

        $cabinetCollection = $this->cabinetService->fetchAll(
            count($from) ? new DateTime($from[0]) : null,
            count($to) ? new DateTime($to[0]) : null
        );

        return new JsonResponse($cabinetCollection, 206);
    }

    /**
     * Create new Resource Assembly.
     *
     * @input \Althingi\Form\Committee
     * @201 Create
     * @205 Update
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $form = new Form\Cabinet([
            ...$request->getParsedBody(),
            'cabinet_id' => $request->getAttribute('id'),
        ]);

        if ($form->isValid()) {
            $affectedRows = $this->cabinetService->save($form->getModel());
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * @input \Althingi\Form\Committee
     * @205 Update
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        if (($committee = $this->cabinetService->get($request->getAttribute('id'))) != null) {
            $form = new Form\Cabinet([
                ...$committee->toArray(),
                ...$request->getParsedBody(),
                'cabinet_id' => $request->getAttribute('id'),
            ]);

            if ($form->isValid()) {
                $this->cabinetService->update($form->getModel());
                return (new EmptyResponse(205));
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\CabinetProperties[]
     * @206 Success
     */
    public function assemblyAction(ServerRequest $request)
    {
        $assemblyId = $request->getAttribute('id');
        $assembly = $this->assemblyService->get($assemblyId);

        $cabinets = $this->cabinetService->fetchAll(
            $assembly->getFrom(),
            $assembly->getTo()
        );

        return new JsonResponse($cabinets, 206);
    }

    public function setCabinetService(Cabinet $cabinet): static
    {
        $this->cabinetService = $cabinet;
        return $this;
    }

    public function setAssemblyService(Assembly $assembly): static
    {
        $this->assemblyService = $assembly;
        return $this;
    }
}
