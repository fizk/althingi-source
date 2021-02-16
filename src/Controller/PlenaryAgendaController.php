<?php
namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequest;
use Althingi\Form;
use Althingi\Service;
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait
};
use Althingi\Injector\ServicePlenaryAgendaAwareInterface;
use Althingi\Utils\ErrorFormResponse;
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};

class PlenaryAgendaController implements
    RestControllerInterface,
    ServicePlenaryAgendaAwareInterface
{
    use RestControllerTrait;
    private Service\PlenaryAgenda $plenaryAgendaService;

    /**
     * @output \Althingi\Model\PlenaryAgendaProperties
     * @200 Success
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $plenaryId  = $request->getAttribute('plenary_id');
        $itemId  = $request->getAttribute('item_id');

        $item = $this->plenaryAgendaService->get($assemblyId, $plenaryId, $itemId);
        return $item
            ? new JsonResponse($item)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\PlenaryAgenda[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $plenaryId  = $request->getAttribute('plenary_id');

        $agenda = $this->plenaryAgendaService->fetch($assemblyId, $plenaryId);

        return new JsonResponse($agenda, 206);
    }

    /**
     * @input \Althingi\Form\PlenaryAgenda
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $plenaryId  = $request->getAttribute('plenary_id');
        $form = new Form\PlenaryAgenda();
        $form->bindValues(array_merge($request->getParsedBody(), [
            'item_id' => $request->getAttribute('item_id'),
            'assembly_id' => $assemblyId,
            'plenary_id' => $plenaryId,
        ]));

        if ($form->isValid()) {
            $object = $form->getObject();
            $affectedRows = $this->plenaryAgendaService->save($object);
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * @input \Althingi\Form\PlenaryAgenda
     * @202 No update
     * @todo does this make sense
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(202);
    }

    public function setPlenaryAgendaService(Service\PlenaryAgenda $plenaryAgenda): self
    {
        $this->plenaryAgendaService = $plenaryAgenda;
        return $this;
    }
}
