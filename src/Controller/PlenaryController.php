<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequest;
use Althingi\Form;
use Althingi\Service\Plenary;
use Althingi\Injector\ServicePlenaryAwareInterface;
use Althingi\Utils\ErrorFormResponse;
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait
};
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};
class PlenaryController implements
    RestControllerInterface,
    ServicePlenaryAwareInterface
{
    use RestControllerTrait;
    private Plenary $plenaryService;

    /**
     * @output \Althingi\Model\Plenary
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $plenaryId = $request->getAttribute('plenary_id');

        $plenary = $this->plenaryService->get($assemblyId, $plenaryId);

        return $plenary
            ? new JsonResponse($plenary, 200)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\Plenary[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id', null);
        $count = $this->plenaryService->countByAssembly($assemblyId);

        $plenaries = $this->plenaryService->fetchByAssembly(
            $assemblyId,
            0, // $range->getFrom(),
            $count
            //($range->getFrom()-$range->getTo())
        );
        return new JsonResponse($plenaries, 206);
    }

    /**
     * @input \Althingi\Form\Plenary
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $form = new Form\Plenary([
            ...$request->getParsedBody(),
            'assembly_id' => $request->getAttribute('id'),
            'plenary_id' => $request->getAttribute('plenary_id'),
        ]);

        if ($form->isValid()) {
            $affectedRows = $this->plenaryService->save($form->getModel());
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return new EmptyResponse(404);
    }

    /**
     * @input \Althingi\Form\Plenary
     * @205 Updated
     * @400 Invalid input
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $plenaryId = $request->getAttribute('plenary_id');

        if (($plenary = $this->plenaryService->get($assemblyId, $plenaryId)) != null) {
            $form = new Form\Plenary([
                ...$plenary->toArray(),
                ...$request->getParsedBody(),
                'assembly_id' => $request->getAttribute('id'),
                'plenary_id' => $request->getAttribute('plenary_id'),
            ]);

            if ($form->isValid()) {
                $this->plenaryService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function setPlenaryService(Plenary $plenary): self
    {
        $this->plenaryService = $plenary;
        return $this;
    }
}
