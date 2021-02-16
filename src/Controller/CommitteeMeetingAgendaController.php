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
use Althingi\Injector\ServiceCommitteeMeetingAgendaAwareInterface;
use Althingi\Utils\ErrorFormResponse;
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait
};

class CommitteeMeetingAgendaController implements
    RestControllerInterface,
    ServiceCommitteeMeetingAgendaAwareInterface
{
    use RestControllerTrait;
    private Service\CommitteeMeetingAgenda $committeeMeetingAgendaService;

    /**
     * @output \Althingi\Model\CommitteeMeetingAgenda
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $agendaId = $request->getAttribute('committee_meeting_agenda_id');
        $meetingId = $request->getAttribute('committee_meeting_id');

        $agenda = $this->committeeMeetingAgendaService->get($meetingId, $agendaId);

        return $agenda
            ? new JsonResponse($agenda, 200)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\CommitteeMeetingAgenda[]
     * @200 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $meetingId = $request->getAttribute('committee_meeting_id');
        $agenda = $this->committeeMeetingAgendaService->fetch($meetingId);
        return new JsonResponse($agenda, 200);
    }

    /**
     * @input \Althingi\Form\CommitteeMeetingAgenda
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $committeeMeetingId = $request->getAttribute('committee_meeting_id');
        $committeeMeetingAgendaId = $request->getAttribute('committee_meeting_agenda_id');

        $form = new Form\CommitteeMeetingAgenda();
        $form->bindValues(array_merge($request->getParsedBody(), [
            'committee_meeting_agenda_id' => $committeeMeetingAgendaId,
            'assembly_id' => $assemblyId,
            'committee_meeting_id' => $committeeMeetingId
        ]));

        if ($form->isValid()) {
            $affectedRows = $this->committeeMeetingAgendaService->save($form->getObject());
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * @input Althingi\Form\CommitteeMeetingAgenda
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        $committeeMeetingId = $request->getAttribute('committee_meeting_id');
        $committeeMeetingAgendaId = $request->getAttribute('committee_meeting_agenda_id');

        if (($agenda = $this->committeeMeetingAgendaService
                ->get($committeeMeetingId, $committeeMeetingAgendaId)) != null) {
            $form = new Form\CommitteeMeetingAgenda();
            $form->bind($agenda);
            $form->setData($request->getParsedBody());

            if ($form->isValid()) {
                $this->committeeMeetingAgendaService->update($form->getData());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function setCommitteeMeetingAgendaService(Service\CommitteeMeetingAgenda $committeeMeetingAgenda): self
    {
        $this->committeeMeetingAgendaService = $committeeMeetingAgenda;
        return $this;
    }
}
