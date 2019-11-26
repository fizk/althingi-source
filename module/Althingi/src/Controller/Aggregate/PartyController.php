<?php

namespace Althingi\Controller\Aggregate;

use Althingi\Injector\ServicePartyAwareInterface;
use Althingi\Service\Party;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\ItemModel;
use Rend\Helper\Http\Range;

class PartyController extends AbstractRestfulController implements
    ServicePartyAwareInterface
{
    use Range;

    /** @var $issueService \Althingi\Service\Party */
    private $partyService;

    /**
     * @param $id
     * @return ItemModel|\Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Party
     * @200 Success
     * @404 Resource not found
     */
    public function get($id)
    {
        $party = $this->partyService->get($this->params('party_id', null));
        return $party
            ? (new ItemModel($party))->setStatus(200)
            : (new ErrorModel('Resource not found'))->setStatus(404);
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
