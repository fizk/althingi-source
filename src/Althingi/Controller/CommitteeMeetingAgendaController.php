<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Lib\ServiceCommitteeMeetingAgendaAwareInterface;
use Althingi\Lib\ServiceCommitteeMeetingAwareInterface;
use Althingi\Service\CommitteeMeeting;
use Althingi\Service\CommitteeMeetingAgenda;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
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

//    public function get($id)
//    {
//        $committeeMeetingId = $this->params('committee_meeting_id');
//
//        if (($meeting = $this->committeeMeetingService->get($committeeMeetingId)) != null) {
//            return (new ItemModel($meeting))
//                ->setStatus(200)
//                ->setOption('Access-Control-Allow-Origin', '*');
//        }
//
//        return $this->notFoundAction();
//    }
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
            $this->committeeMeetingAgendaService->create($form->getObject());
            return (new EmptyModel())
                ->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

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
                    ->setStatus(204);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }

    /**
     * @param CommitteeMeetingAgenda $committeeMeetingAgenda
     */
    public function setCommitteeMeetingAgendaService(CommitteeMeetingAgenda $committeeMeetingAgenda)
    {
        $this->committeeMeetingAgendaService = $committeeMeetingAgenda;
    }
}
