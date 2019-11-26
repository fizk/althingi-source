<?php

namespace Althingi\Controller;

use Althingi\Form;
use Althingi\Injector\ServiceCommitteeSittingAwareInterface;
use Althingi\Injector\ServiceCongressmanAwareInterface;
use Althingi\Injector\ServiceMinisterSittingAwareInterface;
use Althingi\Injector\ServiceMinistryAwareInterface;
use Althingi\Injector\ServicePartyAwareInterface;
use Althingi\Injector\ServiceSessionAwareInterface;
use Althingi\Model\Assembly;
use Althingi\Model\CongressmanPartyProperties;
use Althingi\Model\MinisterSittingProperties;
use Althingi\Service\CommitteeSitting;
use Althingi\Service\Congressman;
use Althingi\Service\MinisterSitting;
use Althingi\Service\Ministry;
use Althingi\Service\Party;
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
    ServiceMinisterSittingAwareInterface,
    ServicePartyAwareInterface,
    ServiceCongressmanAwareInterface,
    ServiceMinistryAwareInterface
{
    /** @var  \Althingi\Service\MinisterSitting */
    private $ministerSittingService;

    /** @var  \Althingi\Service\Party */
    private $partyService;

    /** @var  \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var  \Althingi\Service\Ministry */
    private $ministryService;

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
        $ministerSitting = $this->ministerSittingService->get($id);

        return $ministerSitting
            ? (new ItemModel($ministerSitting))->setStatus(200)
            : (new ErrorModel('Resource Not Found'))->setStatus(404);
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
     * @201 Created
     * @409 Conflict
     * @400 Invalid input
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
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
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

        return (new ErrorModel('Resource Not Found'))
            ->setStatus(404);
    }

    /**
     * @return CollectionModel
     * @output \Althingi\MinisterSittingProperties[]
     * @206 Success
     */
    public function assemblySessionsAction()
    {
        $assemblyId = $this->params('id', 0);
        $congressmanId = $this->params('congressman_id', 0);
        $sittings = $this->ministerSittingService->fetchByCongressmanAssembly($assemblyId, $congressmanId);

        $properties = array_map(function (\Althingi\Model\MinisterSitting $item) use ($assemblyId) {
            $congressman = (new CongressmanPartyProperties())
                ->setCongressman(
                    $this->congressmanService->get($item->getCongressmanId())
                )
                ->setParty(
                    $item->getPartyId() ? $this->partyService->get($item->getPartyId()) : null
                )
                ->setAssembly(
                    (new Assembly())->setAssemblyId($assemblyId)
                );
            return (new MinisterSittingProperties())
                ->setCongressman($congressman)
                ->setMinisterSitting($item)
                ->setMinistry(
                    $this->ministryService->get($item->getMinistryId())
                )
                ;
        }, $sittings);

        return (new CollectionModel($properties))
            ->setStatus(206)
            ->setRange(0, count($properties), count($properties));
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

    /**
     * @param Congressman $congressman
     * @return $this;
     */
    public function setCongressmanService(Congressman $congressman)
    {
        $this->congressmanService = $congressman;
        return $this;
    }

    /**
     * @param \Althingi\Service\Ministry $ministry
     * @return $this
     */
    public function setMinistryService(Ministry $ministry)
    {
        $this->ministryService = $ministry;
        return $this;
    }

    /**
     * @param \Althingi\Service\Party $party
     * @return $this;
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
        return $this;
    }
}
