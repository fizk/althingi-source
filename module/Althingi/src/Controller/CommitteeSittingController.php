<?php

namespace Althingi\Controller;

use Althingi\Form;
use Althingi\Injector\ServiceCommitteeSittingAwareInterface;
use Althingi\Injector\ServiceSessionAwareInterface;
use Althingi\Service\CommitteeSitting;
use Althingi\Service\Session;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;

/**
 * Class CommitteeSittingController
 * @package Althingi\Controller
 */
class CommitteeSittingController extends AbstractRestfulController implements
    ServiceCommitteeSittingAwareInterface
{
    /** @var  \Althingi\Service\CommitteeSitting */
    private $committeeSittingService;

    /**
     * Get sessions by one Congressman.
     * This is therefor a list.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CommitteeSitting
     * @200 Success
     * @404 Resource not found
     */
    public function get($id)
    {
        $committeeSitting = $this->committeeSittingService->get($id);
        return $committeeSitting
            ? (new ItemModel($committeeSitting))->setStatus(200)
            : (new ErrorModel('Resource Not Found'))->setStatus(404);
    }

    /**
     * Get all sessions my congressman.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CommitteeSitting[]
     * @206 Success
     */
    public function getList()
    {
        $congressmanId = $this->params('congressman_id');

        $sessions = $this->committeeSittingService->fetchByCongressman($congressmanId);
        $sessionsCount = count($sessions);

        return (new CollectionModel($sessions))
            ->setStatus(206)
            ->setRange(0, $sessionsCount, $sessionsCount);
    }

    /**
     * Create a new Congressman session.
     *
     * @todo CommitteeSitting do not have IDs coming from althingi.is.
     *  They are created on this server. To be able to update
     *  these entries, the server has to provide the client with
     *  the URI created on the server. This method will try to
     *  create a resource and if the DB responds with a 23000 (ER_DUP_KEY)
     *  it will then try to find the server's ID and create an URI
     *  from that and pass it back in the HTTP's header Location as
     *  well as issuing a 409 response code. The client can then
     *  try to do a PATCH request with the URI provided.
     *
     *  If althingi.is will start to provide a CommitteeSittingIDs, then this will
     *  not be needed as the resource wil be stores via PUSH request.
     *
     *  To facilitate that, create a self::push() method and remove
     *  \Althingi\Service\CommitteeSitting::getIdentifier()
     *
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\CommitteeSitting
     * @201 Created
     * @409 Conflict
     * @400 Invalid input
     */
    public function post($data)
    {
        $congressmanId = $this->params('congressman_id');
        $statusCode = 201;
        $committeeSittingId = 0;

        $form = new Form\CommitteeSitting();
        $form->setData(array_merge($data, ['congressman_id' => $congressmanId]));

        if ($form->isValid()) {
            /** @var $committeeSitting \Althingi\Model\CommitteeSitting */
            $committeeSitting = $form->getObject();

            try {
                $committeeSittingId = $this->committeeSittingService->create($committeeSitting);
                $statusCode = 201;
            } catch (\PDOException $e) {
                // Error: 1022 SQLSTATE: 23000 (ER_DUP_KEY)
                if ($e->errorInfo[1] === 1062) {
                    $committeeSittingId = $this->committeeSittingService->getIdentifier(
                        $committeeSitting->getCongressmanId(),
                        $committeeSitting->getCommitteeId(),
                        $committeeSitting->getAssemblyId(),
                        $committeeSitting->getFrom()
                    );
                    $statusCode = 409;
                } else {
                    return (new ErrorModel($e))
                        ->setStatus(500);
                }
            }

            return (new EmptyModel())
                ->setLocation(
                    $this->url()->fromRoute(
                        'thingmenn/nefndaseta',
                        [
                            'congressman_id' => $congressmanId,
                            'committee_sitting_id' => $committeeSittingId
                        ]
                    )
                )
                ->setStatus($statusCode);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Session
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch($id, $data)
    {
        if (($session = $this->committeeSittingService->get($id)) != null) {
            $form = new Form\CommitteeSitting();
            $form->bind($session);
            $form->setData($data);

            if ($form->isValid()) {
                $this->committeeSittingService->update($form->getData());
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
     * @param CommitteeSitting $committeeSitting
     * @return $this;
     */
    public function setCommitteeSitting(CommitteeSitting $committeeSitting)
    {
        $this->committeeSittingService = $committeeSitting;
        return $this;
    }
}
