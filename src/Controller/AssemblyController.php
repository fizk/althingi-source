<?php

namespace Althingi\Controller;

use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Althingi\Form;
use Althingi\Service\Assembly;
use Althingi\Utils\ErrorFormResponse;
use Althingi\Injector\ServiceAssemblyAwareInterface;
use Althingi\Router\{
    RestControllerTrait,
    RestControllerInterface
};
use Laminas\Diactoros\Response\{
    EmptyResponse,
    JsonResponse
};

class AssemblyController implements
    RestControllerInterface,
    RequestHandlerInterface,
    ServiceAssemblyAwareInterface
{
    use RestControllerTrait;

    private Assembly $assemblyService;

    /**
     * Get one Assembly.
     *
     * @output \Althingi\Model\Assembly
     * @404 not found
     * @200 Success
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $assembly = $this->assemblyService->get($request->getAttribute('id'));
        return $assembly
            ? new JsonResponse($assembly, 200)
            : new EmptyResponse(404);
    }

    /**
     * Return list of Assemblies.
     *
     * @output \Althingi\Model\Assembly[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $assemblies = $this->assemblyService->fetchAll();
        return new JsonResponse($assemblies, 206);
    }

    /**
     * @200 Success
     */
    public function optionsList(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(200, [
            'Allow' => 'GET, OPTIONS'
        ]);
    }

    /**
     * @200 Success
     */
    public function options(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(200, [
            'Allow' => 'GET, OPTIONS, PUT, PATCH'
        ]);
    }

    /**
     * Update or create one assembly.
     *
     * @input \Althingi\Form\Assembly
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $form = new Form\Assembly([
            ...$request->getParsedBody(),
            'assembly_id' => $request->getAttribute('id'),
        ]);

        if ($form->isValid()) {
            $object = $form->getModel();
            $affectedRows = $this->assemblyService->save($object);
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * Partially update one assembly.
     *
     * @input \Althingi\Form\Assembly
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        if (($assembly = $this->assemblyService->get($request->getAttribute('id'))) != null) {
            $form = new Form\Assembly([
                ...$assembly->toArray(),
                ...$request->getParsedBody(),
                'assembly_id' => $request->getAttribute('id'),
            ]);

            if ($form->isValid()) {
                $this->assemblyService->update($form->getModel());
                return new EmptyResponse(205);
            }
            return new EmptyResponse(400);
        }
        return new EmptyResponse(404);
    }

    public function setAssemblyService(Assembly $assembly): static
    {
        $this->assemblyService = $assembly;
        return $this;
    }
}
