<?php

namespace Althingi\Controller\Aggregate;

use Althingi\Injector\ServiceCongressmanAwareInterface;
use Althingi\Injector\ServiceConstituencyAwareInterface;
use Althingi\Injector\ServicePartyAwareInterface;
use Althingi\Service\Congressman;
use Althingi\Service\Constituency;
use Althingi\Service\Party;
use Althingi\Utils\CategoryParam;
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

    use CategoryParam;


    /** @var $issueService \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var $issueService \Althingi\Service\Party */
    private $partyService;

    /** @var $issueService \Althingi\Service\Constituency */
    private $constituencyService;

    public function getAction()
    {
        $id = $this->params('congressman_id', null);
        return (new ItemModel($this->congressmanService->get($id)));
    }

    /**
     * Get all party by congressman,
     * If `date` is provided, only party on that date is returned.
     *
     * @return \Rend\View\Model\ModelInterface|array
     * @output \Althingi\Model\Party | \Althingi\Model\Party[]
     * @query date string | null
     */
    public function partyAction()
    {
        $id = $this->params('congressman_id', null);
        $date = $this->params()->fromQuery('dags', null);
        if ($date) {
            return (new ItemModel($this->partyService->getByCongressman($id, new DateTime($date))));
        } else {
            return (new CollectionModel($this->partyService->fetchByCongressman($id)));
        }
    }

    /**
     * Get all constituencies by congressman,
     * If `date` is provided, only constituency on that date is returned.
     *
     * @return \Rend\View\Model\ModelInterface|array
     * @output \Althingi\Model\ConstituencyDate | \Althingi\Model\ConstituencyDate[]
     * @query date string | null
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
