<?php

namespace Althingi\Controller;

use Althingi\Lib\ServiceCommitteeMeetingAgendaAwareInterface;
use Althingi\Service\CommitteeMeetingAgenda;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;
use Althingi\Form\CommitteeMeetingAgenda as CommitteeMeetingAgendaForm;
use Rend\View\Model\ItemModel;

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
     */
    public function get($id)
    {
        $agendaId = $this->params('committee_meeting_agenda_id');
        $meetingId = $this->params('committee_meeting_id');

        $agenda = $this->committeeMeetingAgendaService->get($meetingId, $agendaId);

        return $agenda
            ? (new ItemModel($agenda))
            : $this->notFoundAction();
    }

//
//    public function getList()
//    {
//        $assemblyId = $this->params('id');
//        $committeeId = $this->params('committee_id');
//
//        $meetings = $this->committeeMeetingService->fetchByAssembly($assemblyId, $committeeId);
//
//        return (new CollectionModel($meetings));
//    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\CommitteeMeetingAgenda
     */
    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $committeeMeetingId = $this->params('committee_meeting_id');
        $committeeMeetingAgendaId = $this->params('committee_meeting_agenda_id');

        $form = new CommitteeMeetingAgendaForm();
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

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     * @input Althingi\Form\CommitteeMeetingAgenda
     */
    public function patch($id, $data)
    {
        $committeeMeetingId = $this->params('committee_meeting_id');
        $committeeMeetingAgendaId = $this->params('committee_meeting_agenda_id');

        if (($agenda = $this->committeeMeetingAgendaService
                ->get($committeeMeetingId, $committeeMeetingAgendaId)) != null) {
            $form = new CommitteeMeetingAgendaForm();
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

        return $this->notFoundAction();
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
