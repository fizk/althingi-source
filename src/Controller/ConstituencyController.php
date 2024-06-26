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
use DateTime;

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
        $form = new Form\Constituency([
            ...$request->getParsedBody(),
            'constituency_id' => $request->getAttribute('id'),
        ]);
        if ($form->isValid()) {
            $affectedRows = $this->constituencyService->save($form->getModel());
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
            $form = new Form\Constituency([
                ...$constituency->toArray(),
                ...$request->getParsedBody(),
                'constituency_id' => $request->getAttribute('id')
            ]);

            if ($form->isValid()) {
                $this->constituencyService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function getByCongressmanAction(ServerRequest $request): ResponseInterface
    {
        $params = $request->getQueryParams();
        if (array_key_exists('dags', $params)) {
            $constituencies = $this->constituencyService->getByCongressman(
                $request->getAttribute('congressman_id'),
                new DateTime($params['dags'])
            );
            return new JsonResponse([$constituencies]);
        } else {
            return new JsonResponse([]);
        }
    }

    public function setConstituencyService(Service\Constituency $constituency): self
    {
        $this->constituencyService = $constituency;
        return $this;
    }
}
