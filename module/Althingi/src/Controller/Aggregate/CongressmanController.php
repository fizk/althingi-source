<?php

namespace Althingi\Controller\Aggregate;

use Althingi\Injector\ServiceCongressmanAwareInterface;
use Althingi\Injector\ServiceConstituencyAwareInterface;
use Althingi\Injector\ServicePartyAwareInterface;
use Althingi\Model\CongressmanAndParty;
use Althingi\Model\CongressmanPartyProperties;
use Althingi\Service\Congressman;
use Althingi\Service\Constituency;
use Althingi\Service\Party;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;
use DateTime;

class CongressmanController extends AbstractRestfulController implements
    ServiceCongressmanAwareInterface,
    ServicePartyAwareInterface,
    ServiceConstituencyAwareInterface
{
    use Range;

    /** @var $issueService \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var $issueService \Althingi\Service\Party */
    private $partyService;

    /** @var $issueService \Althingi\Service\Constituency */
    private $constituencyService;

    /**
     * Get a single congressman.
     *
     * If date is provided, party and constituency are provided as well.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CongressmanPartyProperties | \Althingi\Model\Congressman
     * @query dags
     * @throws \Exception
     * @200 Success
     */
    public function getAction()
    {
        $id = $this->params('congressman_id', null);
        $date = $this->params()->fromQuery('dags', null);
        $assemblyId = $this->params()->fromQuery('loggjafarthing', null);

        if ($date) {
            $congressman = (new CongressmanPartyProperties())
                ->setCongressman($this->congressmanService->get(
                    $id
                ))->setConstituency($this->constituencyService->getByCongressman(
                    $id,
                    new DateTime($date)
                ))->setParty($this->partyService->getByCongressman(
                    $id,
                    new DateTime($date)
                ));
            return (new ItemModel($congressman));
        } elseif ($assemblyId) {
            $congressman = (new CongressmanPartyProperties())
                ->setCongressman($this->congressmanService->get(
                    $id
                ))->setConstituency($this->constituencyService->getByCongressmanAndConstituency(
                    $id,
                    $assemblyId
                ))->setParty($this->partyService->getByCongressmanAndAssembly(
                    $id,
                    $assemblyId
                ));
            return (new ItemModel($congressman))->setStatus(200);
        } else {
            return (new ItemModel($this->congressmanService->get($id)))->setStatus(200);
        }
    }

    /**
     * Get all party by congressman,
     * If `date` is provided, only party on that date is returned.
     *
     * @return \Rend\View\Model\ModelInterface|array
     * @output \Althingi\Model\Party | \Althingi\Model\Party[]
     * @query date string | null
     * @throws \Exception
     * @200 Success
     * @206 Success
     */
    public function partyAction()
    {
        $id = $this->params('congressman_id', null);
        $date = $this->params()->fromQuery('dags', null);
        if ($date) {
            return (new ItemModel($this->partyService->getByCongressman($id, new DateTime($date))))
                ->setStatus(200);
        } else {
            return (new CollectionModel($this->partyService->fetchByCongressman($id)))
                ->setStatus(206);
        }
    }

    /**
     * Get all constituencies by congressman,
     * If `date` is provided, only constituency on that date is returned.
     *
     * @return \Rend\View\Model\ModelInterface|array
     * @output \Althingi\Model\ConstituencyDate | \Althingi\Model\ConstituencyDate[]
     * @query date string | null
     * @throws \Exception
     * @200 Success
     * @206 Success
     */
    public function constituencyAction()
    {
        $id = $this->params('congressman_id', null);
        $date = $this->params()->fromQuery('dags', null);

        if ($date) {
            return (new ItemModel($this->constituencyService->getByCongressman($id, new DateTime($date))));
        } else {
            return (new CollectionModel($this->constituencyService->fetchByCongressman($id)));
        }
    }

    /**
     * @param Party $party
     * @return $this
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
        return $this;
    }

    /**
     * @param Congressman $congressman
     * @return $this
     */
    public function setCongressmanService(Congressman $congressman)
    {
        $this->congressmanService = $congressman;
        return $this;
    }

    /**
     * @param Constituency $constituency
     * @return $this
     */
    public function setConstituencyService(Constituency $constituency)
    {
        $this->constituencyService = $constituency;
        return $this;
    }
}
