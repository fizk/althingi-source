<?php

namespace Althingi\Controller;

use Althingi\Form;
use Althingi\Injector\ServiceCommitteeSittingAwareInterface;
use Althingi\Injector\ServiceMinisterSittingAwareInterface;
use Althingi\Injector\ServiceSessionAwareInterface;
use Althingi\Service\CommitteeSitting;
use Althingi\Service\MinisterSitting;
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
class MinisterSittingController extends AbstractRestfulController implements
    ServiceMinisterSittingAwareInterface
{
    /** @var  \Althingi\Service\MinisterSitting */
    private $ministerSittingService;

    /**
     * Get sessions by one Congressman.
     * This is therefor a list.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CommitteeSitting
     */
    public function get($id)
    {
        if (($ministerSitting = $this->ministerSittingService->get($id)) != null) {
            return new ItemModel($ministerSitting);
        }

        return $this->notFoundAction();
    }

    /**
     * Create a new Congressman session.
     *
     * @todo MinisterSitting do not have IDs coming from althingi.is.
     *  They are created on this server. To be able to update
     *  these entries, the server has to provide the client with
     *  the URI created on the server. This method will try to
     *  create a resource and if the DB responds with a 23000 (ER_DUP_KEY)
     *  it will then try to find the server's ID and create an URI
     *  from that and pass it back in the HTTP's header Location as
     *  well as issuing a 409 response code. The client can then
     *  try to do a PATCH request with the URI provided.
     *
     *  If althingi.is will start to provide a MinisterSittingIDs, then this will
     *  not be needed as the resource wil be stores via PUSH request.
     *
     *  To facilitate that, create a self::push() method and remove
     *  \Althingi\Service\MinisterSitting::getIdentifier()
     *
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\CommitteeSitting
     */
    public function post($data)
    {
        $congressmanId = $this->params('congressman_id');
        $statusCode = 201;
        $ministerSittingId = 0;

        $form = new Form\MinisterSitting();
        $form->setData(array_merge($data, ['congressman_id' => $congressmanId]));

        if ($form->isValid()) {
            /** @var $ministerSitting \Althingi\Model\MinisterSitting */
            $ministerSitting = $form->getObject();

            try {
                $ministerSittingId = $this->ministerSittingService->create($ministerSitting);
                $statusCode = 201;
            } catch (\Exception $e) {
                // Error: 1022 SQLSTATE: 23000 (ER_DUP_KEY)
                if ($e->getCode() == 23000) {
                    $ministerSittingId = $this->ministerSittingService->getIdentifier(
                        $ministerSitting->getAssemblyId(),
                        $ministerSitting->getMinistryId(),
                        $ministerSitting->getCongressmanId(),
                        $ministerSitting->getFrom()
                    );
                    $statusCode = 409;
                }
            }

            return (new EmptyModel())
                ->setLocation(
                    $this->url()->fromRoute(
                        'thingmenn/radherraseta',
                        [
                            'congressman_id' => $congressmanId,
                            'ministry_sitting_id' => $ministerSittingId
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
     */
    public function patch($id, $data)
    {
        if (($session = $this->ministerSittingService->get($id)) != null) {
            $form = new Form\MinisterSitting();
            $form->bind($session);
            $form->setData($data);

            if ($form->isValid()) {
                $this->ministerSittingService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }

    /**
     * @param \Althingi\Service\MinisterSitting $ministerSitting
     * @return $this;
     */
    public function setMinisterSittingService(MinisterSitting $ministerSitting)
    {
        $this->ministerSittingService = $ministerSitting;
        return $this;
    }
}
