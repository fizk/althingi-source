<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Lib\ServiceCommitteeMeetingAwareInterface;
use Althingi\Service\CommitteeMeeting;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;
use Althingi\Form\CommitteeMeeting as CommitteeMeetingForm;
use Rend\View\Model\ItemModel;

class CommitteeMeetingController extends AbstractRestfulController implements
    ServiceCommitteeMeetingAwareInterface
{
    /**
     * @var \Althingi\Service\CommitteeMeeting
     */
    private $committeeMeetingService;

    public function get($id)
    {
        $committeeMeetingId = $this->params('committee_meeting_id');

        if (($meeting = $this->committeeMeetingService->get($committeeMeetingId)) != null) {
            return (new ItemModel($meeting))
                ->setStatus(200)
                ->setOption('Access-Control-Allow-Origin', '*');
        }

        return $this->notFoundAction();
    }

    public function getList()
    {
        $assemblyId = $this->params('id');
        $committeeId = $this->params('committee_id');

        $meetings = $this->committeeMeetingService->fetchByAssembly($assemblyId, $committeeId);

        return (new CollectionModel($meetings));
    }

    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $committeeId = $this->params('committee_id');
        $committeeMeetingId = $this->params('committee_meeting_id');

        $form = new CommitteeMeetingForm();
        $form->bindValues(array_merge($data, [
            'committee_id' => $committeeId,
            'assembly_id' => $assemblyId,
            'committee_meeting_id' => $committeeMeetingId
        ]));
        if ($form->isValid()) {
            $this->committeeMeetingService->create($form->getObject());
            return (new EmptyModel())
                ->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    public function patch($id, $data)
    {
        $committeeMeetingId = $this->params('committee_meeting_id');

        if (($party = $this->committeeMeetingService->get($committeeMeetingId)) != null) {
            $form = new CommitteeMeetingForm();
            $form->bind($party);
            $form->setData($data);

            if ($form->isValid()) {
                $this->committeeMeetingService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(204);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }

    /**
     * @param CommitteeMeeting $committeeMeeting
     */
    public function setCommitteeMeetingService(CommitteeMeeting $committeeMeeting)
    {
        $this->committeeMeetingService = $committeeMeeting;
    }
}
