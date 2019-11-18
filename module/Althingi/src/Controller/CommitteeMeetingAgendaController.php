<?php

namespace Althingi\Controller;

use Althingi\Injector\ServiceCommitteeMeetingAgendaAwareInterface;
use Althingi\Service\CommitteeMeetingAgenda;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\ItemModel;
use Althingi\Form;

class CommitteeMeetingAgendaController extends AbstractRestfulController implements
    ServiceCommitteeMeetingAgendaAwareInterface
{
    /**
     * @var \Althingi\Service\CommitteeMeetingAgenda
     */
    private $committeeMeetingAgendaService;

    /**
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CommitteeMeetingAgenda
     * @200 Success
     * @404 Resource not found
     */
    public function get($id)
    {
        $agendaId = $this->params('committee_meeting_agenda_id');
        $meetingId = $this->params('committee_meeting_id');

        $agenda = $this->committeeMeetingAgendaService->get($meetingId, $agendaId);

        return $agenda
            ? (new ItemModel($agenda))->setStatus(200)
            : (new ErrorModel('Resource Not Found'))->setStatus(404);
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\CommitteeMeetingAgenda
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $committeeMeetingId = $this->params('committee_meeting_id');
        $committeeMeetingAgendaId = $this->params('committee_meeting_agenda_id');

        $form = new Form\CommitteeMeetingAgenda();
        $form->bindValues(array_merge($data, [
            'committee_meeting_agenda_id' => $committeeMeetingAgendaId,
            'assembly_id' => $assemblyId,
            'committee_meeting_id' => $committeeMeetingId
        ]));

        if ($form->isValid()) {
            $affectedRows = $this->committeeMeetingAgendaService->save($form->getObject());
            return (new EmptyModel())
                ->setStatus($affectedRows === 1 ? 201 : 205);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     * @input Althingi\Form\CommitteeMeetingAgenda
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch($id, $data)
    {
        $committeeMeetingId = $this->params('committee_meeting_id');
        $committeeMeetingAgendaId = $this->params('committee_meeting_agenda_id');

        if (($agenda = $this->committeeMeetingAgendaService
                ->get($committeeMeetingId, $committeeMeetingAgendaId)) != null) {
            $form = new Form\CommitteeMeetingAgenda();
            $form->bind($agenda);
            $form->setData($data);

            if ($form->isValid()) {
                $this->committeeMeetingAgendaService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return (new ErrorModel('Resource Not Found'))
            ->setStatus(404);
    }

    /**
     * @param CommitteeMeetingAgenda $committeeMeetingAgenda
     * @return $this
     */
    public function setCommitteeMeetingAgendaService(CommitteeMeetingAgenda $committeeMeetingAgenda)
    {
        $this->committeeMeetingAgendaService = $committeeMeetingAgenda;
        return $this;
    }
}
