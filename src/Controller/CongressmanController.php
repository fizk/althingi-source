<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Althingi\Form;
use Althingi\Injector\ServiceCongressmanAwareInterface;
use Althingi\Service;
use Althingi\Utils\ErrorFormResponse;
use Althingi\Router\{
    RestControllerTrait,
    RestControllerInterface
};
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};

class CongressmanController implements
    RestControllerInterface,
    ServiceCongressmanAwareInterface
{
    use RestControllerTrait;
    private Service\Congressman $congressmanService;

    /**
     * Get one congressman.
     *
     * @output \Althingi\Model\CongressmanAndParties
     * @200 Success
     * 404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $congressman = $this->congressmanService->get(
            $request->getAttribute('congressman_id')
        );
        return $congressman
            ? new JsonResponse($congressman)
            : new EmptyResponse(404);
    }

    /**
     * Return list of congressmen.
     *
     * @output \Althingi\Model\CongressmanAndParties[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $congressmen = $this->congressmanService->fetchAll();
        return new JsonResponse($congressmen, 206);
    }

    /**
     * Create on congressman entry.
     *
     * @input \Althingi\Form\Congressman
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $form = new Form\Congressman([
            ...$request->getParsedBody(),
            'congressman_id' => $request->getAttribute('congressman_id'),
        ]);

        if ($form->isValid()) {
            $affectedRows = $this->congressmanService->save($form->getModel());
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * Update congressman.
     *
     * @input \Althingi\Form\Congressman
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        if (($congressman = $this->congressmanService->get(
            $request->getAttribute('congressman_id')
        )) != null) {
            $form = new Form\Congressman([
                ...$congressman->toArray(),
                ...$request->getParsedBody(),
                'congressman_id' => $request->getAttribute('congressman_id'),
            ]);

            if ($form->isValid()) {
                $this->congressmanService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    /**
     * @205 Deleted
     * @404 Resource not found
     */
    public function delete(ServerRequest $request): ResponseInterface
    {
        if (($congressman = $this->congressmanService->get($request->getAttribute('congressman_id'))) !== null) {
            $this->congressmanService->delete($congressman->getCongressmanId());
            return new EmptyResponse(205);
        }

        return new EmptyResponse(404);
    }

    /**
     * Entry option for Congressman entry.
     *
     * @200 Success
     */
    public function optionsList(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(200, ['Allow' => 'GET, OPTIONS']);
    }

    /**
     * List options for Congressman entry.
     *
     * @200 Success
     */
    public function options(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(200, ['Allow' => 'GET, OPTIONS, PUT, PATCH, DELETE']);
    }

    /**
     * Get all members of assembly.
     *
     * @output \Althingi\Model\CongressmanPartyProperties[]
     * @query tegund thingmadur|varamadur
     * @206 Success
     * @deprecated
     */
    public function assemblyAction(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $typeArray = [
            'thingmadur' => Service\Congressman::CONGRESSMAN_TYPE_MP,
            'varamadur' => Service\Congressman::CONGRESSMAN_TYPE_SUBSTITUTE
        ];
        $typeQuery = $request->getQueryParams()['tegund'] ?? null;
        $typeParam = array_key_exists($typeQuery, $typeArray) ? $typeArray[$typeQuery] : null;

        $congressmen = $this->congressmanService->fetchByAssembly($assemblyId, $typeParam);

        return new JsonResponse($congressmen, 206);
    }

    /**
     * Gets a single Congressman in an assembly including all parties
     * and constituency.
     *
     * @output \Althingi\Model\CongressmanPartyProperties
     * @200 Success
     * @404 Resource not found
     * @deprecated
     */
    public function assemblyCongressmanAction(ServerRequest $request): ResponseInterface
    {
        $congressmanId = $request->getAttribute('congressman_id');

        $congressman = $this->congressmanService->get($congressmanId);
        return $congressman
            ? new JsonResponse($congressman)
            : new EmptyResponse(404);
    }

    /**
     * @param Congressman $congressman
     * @return $this
     */
    public function setCongressmanService(Service\Congressman $congressman): self
    {
        $this->congressmanService = $congressman;
        return $this;
    }
}
