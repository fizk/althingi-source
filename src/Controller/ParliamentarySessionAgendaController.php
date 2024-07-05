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
use Althingi\Injector\ServiceParliamentarySessionAgendaAwareInterface;
use Althingi\Utils\ErrorFormResponse;
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};
use PDOException;

class ParliamentarySessionAgendaController implements
    RestControllerInterface,
    ServiceParliamentarySessionAgendaAwareInterface,
    ServiceIssueAwareInterface
{
    use RestControllerTrait;

    private Service\ParliamentarySessionAgenda $parliamentarySessionAgendaService;
    private Service\Issue $issueService;

    /**
     * @output \Althingi\Model\ParliamentarySessionAgendaProperties
     * @200 Success
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $item = $this->parliamentarySessionAgendaService->get(
            $request->getAttribute('id'),
            $request->getAttribute('parliamentary_session_id'),
            $request->getAttribute('item_id')
        );
        return $item
            ? new JsonResponse($item)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\ParliamentarySessionAgenda[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $agenda = $this->parliamentarySessionAgendaService->fetch(
            $request->getAttribute('id'),
            $request->getAttribute('parliamentary_session_id')
        );

        return new JsonResponse($agenda, 206);
    }

    /**
     * @input \Althingi\Form\ParliamentarySessionAgenda
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $form = new Form\ParliamentarySessionAgenda([
            ...$request->getParsedBody(),
            'item_id' => $request->getAttribute('item_id'),
            'assembly_id' => $request->getAttribute('id'),
            'parliamentary_session_id' => $request->getAttribute('parliamentary_session_id'),
        ]);

        if ($form->isValid()) {
            $object = $form->getModel();
            try {
                $affectedRows = $this->parliamentarySessionAgendaService->save($object);
                return new EmptyResponse($affectedRows === 1 ? 201 : 205);

            // @FIXME if you can
            // Sometimes ParliamentarySessionAgenda items will contain (usually a B) issue that
            //  doesn't exist. It's not in the list of Issues for this Assembly, but
            //  then shows up in the Agenda.
            // This results in ForeignKeyConstraint, where the Agenda Item can't be added
            //  because the Issue is not in the DB
            // This is just a hack, where if there is a PDOException which REFERENCES `Issue
            //  the Issue is created with minimum data and then the Agenda Item is re-tried
            } catch (PDOException $e) {
                if (str_contains($e->getMessage(), 'REFERENCES `Issue`')) {
                    $data = $form->getModel();
                    $this->issueService->create((new IssueModel())
                        ->setIssueId($data->getIssueId())
                        ->setAssemblyId($data->getAssemblyId())
                        ->setKind($data->getKind())
                        ->setName(null)
                        ->setType(null)
                        ->setTypeName(null));
                    $affectedRows = $this->parliamentarySessionAgendaService->save($object);
                    return new EmptyResponse($affectedRows === 1 ? 201 : 205);
                }
                throw $e;
            }
        }

        return new ErrorFormResponse($form);
    }

    /**
     * @input \Althingi\Form\ParliamentarySessionAgenda
     * @202 No update
     * @todo does this make sense
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(202);
    }

    public function setParliamentarySessionAgendaService(
        Service\ParliamentarySessionAgenda $parliamentarySessionAgenda
    ): static {
        $this->parliamentarySessionAgendaService = $parliamentarySessionAgenda;
        return $this;
    }
    /**
     * Set service.
     *
     * @return $this;
     */
    public function setIssueService(Service\Issue $issue): static
    {
        $this->issueService = $issue;
        return $this;
    }
}
