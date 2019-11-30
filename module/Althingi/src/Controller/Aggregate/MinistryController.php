<?php

namespace Althingi\Controller\Aggregate;

use Althingi\Injector\ServiceMinistryAwareInterface;
use Althingi\Service\Ministry;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\ItemModel;
use Rend\Helper\Http\Range;

class MinistryController extends AbstractRestfulController implements
    ServiceMinistryAwareInterface
{
    use Range;

    /** @var $issueService \Althingi\Service\Ministry */
    private $ministryService;

    /**
     * @param $id
     * @return ItemModel|\Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Ministry
     * @200 Success
     * @404 Resource not found
     */
    public function get($id)
    {
        $party = $this->ministryService->get($this->params('ministry_id', null));
        return $party
            ? (new ItemModel($party))->setStatus(200)
            : (new ErrorModel('Resource not found'))->setStatus(404);
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
}
