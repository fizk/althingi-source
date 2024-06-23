<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};
use Althingi\Form;
use Althingi\Injector\ServicePresidentAwareInterface;
use Althingi\Service\President;
use Althingi\Utils\{
    ErrorExceptionResponse,
    ErrorFormResponse
};
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait,
    RouteInterface,
    RouterAwareInterface
};

class PresidentController implements
    RestControllerInterface,
    ServicePresidentAwareInterface,
    RouterAwareInterface
{
    use RestControllerTrait;
    private RouteInterface $router;
    private President $presidentService;

    /**
     * @output \Althingi\Model\President
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $president = $this->presidentService->get(
            $request->getAttribute('id')
        );

        return $president
            ? new JsonResponse($president)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\PresidentPartyProperties[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $presidents = $this->presidentService->fetch();
        return new JsonResponse($presidents, 206);
    }

    /**
     * @input \Althingi\Form\President
     * @201 Created
     * @409 Conflict
     * @400 Invalid input
     */
    public function post(ServerRequest $request): ResponseInterface
    {
        $form = new Form\President([
            ...$request->getParsedBody(),
        ]);

        if ($form->isValid()) {
            /** @var \Althingi\Model\President */
            $newPresident = $form->getModel();
            $statusCode = 201;
            $presidentId = 0;

            try {
                $presidentId = $this->presidentService->create($newPresident);
            } catch (\PDOException $e) {
                if ($e->errorInfo[1] == 1062) {
                    $existingPresident = $this->presidentService->getByUnique(
                        $newPresident->getAssemblyId(),
                        $newPresident->getCongressmanId(),
                        $newPresident->getFrom(),
                        $newPresident->getTitle()
                    );
                    $presidentId = $existingPresident->getPresidentId();
                    $statusCode = 409;
                } else {
                    return new ErrorExceptionResponse($e);
                }
            }

            return new EmptyResponse($statusCode, [
                'Location' => $this->router->assemble([
                    'id' => $presidentId
                ], ['name' => 'forsetar'])
            ]);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * @input \Althingi\Form\President
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        if (($president = $this->presidentService->get(
            $request->getAttribute('id')
        )) != null) {
            $form = new Form\President([
                ...(new \Althingi\Hydrator\President())->extract($president),
                ...$request->getParsedBody(),
                // 'president_id' => $request->getAttribute('id'),
            ]);

            if ($form->isValid()) {
                $this->presidentService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function setPresidentService(President $president): self
    {
        $this->presidentService = $president;
        return $this;
    }

    public function setRouter(RouteInterface $router): self
    {
        $this->router = $router;
        return $this;
    }
}
