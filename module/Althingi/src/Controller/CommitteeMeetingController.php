<?php

namespace Althingi\Controller;

use Althingi\Injector\ServiceCommitteeMeetingAwareInterface;
use Althingi\Service\CommitteeMeeting;
use Althingi\Form;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\ItemModel;

class CommitteeMeetingController extends AbstractRestfulController implements
    ServiceCommitteeMeetingAwareInterface
{
    /**
     * @var \Althingi\Service\CommitteeMeeting
     */
    private $committeeMeetingService;

    /**
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CommitteeMeeting
     * @200 Success
     * @404 Resource not found
     */
    public function get($id)
    {
        $committeeMeetingId = $this->params('committee_meeting_id');

        if (($meeting = $this->committeeMeetingService->get($committeeMeetingId)) != null) {
            return (new ItemModel($meeting))
                ->setStatus(200)
                ->setOption('Access-Control-Allow-Origin', '*');
        }

        return (new ErrorModel('Resource Not Found'))
            ->setStatus(404);
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CommitteeMeeting[]
     * @206 Success
     */
    public function getList()
    {
        $assemblyId = $this->params('id');
        $committeeId = $this->params('committee_id');

        $meetings = $this->committeeMeetingService->fetchByAssembly($assemblyId, $committeeId);

        return (new CollectionModel($meetings))
            ->setStatus(206)
            ->setRange(0, count($meetings), count($meetings));
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input Althingi\Form\CommitteeMeeting
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $committeeId = $this->params('committee_id');
        $committeeMeetingId = $this->params('committee_meeting_id');

        $form = new Form\CommitteeMeeting();
        $form->bindValues(array_merge($data, [
            'committee_id' => $committeeId,
            'assembly_id' => $assemblyId,
            'committee_meeting_id' => $committeeMeetingId
        ]));
        if ($form->isValid()) {
            $affectedRows = $this->committeeMeetingService->save($form->getObject());
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
     * @input Althingi\Form\CommitteeMeeting
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch($id, $data)
    {
        $committeeMeetingId = $this->params('committee_meeting_id');

        if (($committeeMeeting = $this->committeeMeetingService->get($committeeMeetingId)) != null) {
            $form = new Form\CommitteeMeeting();
            $form->bind($committeeMeeting);
            $form->setData($data);

            if ($form->isValid()) {
                $this->committeeMeetingService->update($form->getData());
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
     * @param CommitteeMeeting $committeeMeeting
     * @return $this
     */
    public function setCommitteeMeetingService(CommitteeMeeting $committeeMeeting)
    {
        $this->committeeMeetingService = $committeeMeeting;
        return $this;
    }
}
