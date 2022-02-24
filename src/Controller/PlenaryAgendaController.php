<?php
namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequest;
use Althingi\Model\Issue as IssueModel;
use Althingi\Form;
use Althingi\Service;
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait
};
use Althingi\Injector\ServiceIssueAwareInterface;
use Althingi\Injector\ServicePlenaryAgendaAwareInterface;
use Althingi\Utils\ErrorFormResponse;
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};
use PDOException;

class PlenaryAgendaController implements
    RestControllerInterface,
    ServicePlenaryAgendaAwareInterface,
    ServiceIssueAwareInterface
{
    use RestControllerTrait;
    private Service\PlenaryAgenda $plenaryAgendaService;
    private Service\Issue $issueService;

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
        $form->setData(array_merge($request->getParsedBody(), [
            'item_id' => $request->getAttribute('item_id'),
            'assembly_id' => $assemblyId,
            'plenary_id' => $plenaryId,
        ]));

        if ($form->isValid()) {
            $object = $form->getObject();
            try {
                $affectedRows = $this->plenaryAgendaService->save($object);
                return new EmptyResponse($affectedRows === 1 ? 201 : 205);

            // @FIXME if you can
            // Sometimes PlenaryAgenda items will contain (usually a B) issue that
            //  doesn't exist. It's not in the list of Issues for this Assembly, but
            //  then shows up in the Agenda.
            // This results in ForeignKeyConstraint, where the Agenda Item can't be added
            //  because the Issue is not in the DB
            // This is just a hack, where if there is a PDOException which REFERENCES `Issue
            //  the Issue is created with minimum data and then the Agenda Item is re-tried
            } catch (PDOException $e) {
                if (str_contains($e->getMessage(), 'REFERENCES `Issue`')) {
                    $data = $form->getData();
                    $this->issueService->create((new IssueModel())
                        ->setIssueId(isset($data['issue_id']) ? $data['issue_id'] : null)
                        ->setAssemblyId(isset($data["assembly_id"]) ? $data["assembly_id"] : null)
                        ->setCategory(isset($data["category"]) ? $data["category"] : null)
                        ->setName(isset($data["issue_name"]) ? $data["issue_name"] : null)
                        ->setType(isset($data["issue_type"]) ? $data["issue_type"] : null)
                        ->setTypeName(isset($data["issue_typename"]) ? $data["issue_typename"] : null));

                    $affectedRows = $this->plenaryAgendaService->save($object);
                    return new EmptyResponse($affectedRows === 1 ? 201 : 205);
                }
                throw $e;
            }
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
    /**
     * Set service.
     *
     * @return $this;
     */
    public function setIssueService(Service\Issue $issue): self
    {
        $this->issueService = $issue;
        return $this;
    }
}
