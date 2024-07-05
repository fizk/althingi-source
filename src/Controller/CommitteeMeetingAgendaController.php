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
        $agenda = $this->committeeMeetingAgendaService->get(
            $request->getAttribute('committee_meeting_id'),
            $request->getAttribute('committee_meeting_agenda_id')
        );

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
        $agenda = $this->committeeMeetingAgendaService->fetch(
            $request->getAttribute('committee_meeting_id')
        );
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
        $form = new Form\CommitteeMeetingAgenda([
            ...$request->getParsedBody(),
            'committee_meeting_agenda_id' => $request->getAttribute('committee_meeting_agenda_id'),
            'assembly_id' => $request->getAttribute('id'),
            'committee_meeting_id' => $request->getAttribute('committee_meeting_id'),
        ]);

        if ($form->isValid()) {
            $affectedRows = $this->committeeMeetingAgendaService->save($form->getModel());
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

        if (
            ($committeeMeetingAgenda = $this->committeeMeetingAgendaService->get(
                $committeeMeetingId,
                $committeeMeetingAgendaId
            )) != null
        ) {
            $form = new Form\CommitteeMeetingAgenda([
                ...$committeeMeetingAgenda->toArray(),
                ...$request->getParsedBody(),
                'committee_meeting_id' => $request->getAttribute('committee_meeting_id'),
                'committee_meeting_agenda_id' => $request->getAttribute('committee_meeting_agenda_id'),
            ]);

            if ($form->isValid()) {
                $this->committeeMeetingAgendaService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function setCommitteeMeetingAgendaService(Service\CommitteeMeetingAgenda $committeeMeetingAgenda): static
    {
        $this->committeeMeetingAgendaService = $committeeMeetingAgenda;
        return $this;
    }
}
