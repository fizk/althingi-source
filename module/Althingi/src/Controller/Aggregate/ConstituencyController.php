<?php

namespace Althingi\Controller\Aggregate;

use Althingi\Injector\ServiceConstituencyAwareInterface;
use Althingi\Service\Constituency;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\ItemModel;
use Rend\Helper\Http\Range;

class ConstituencyController extends AbstractRestfulController implements
    ServiceConstituencyAwareInterface
{
    use Range;

    /** @var $issueService \Althingi\Service\Constituency */
    private $constituencyService;

    /**
     * @param $id
     * @return ItemModel|\Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Constituency
     * @200 Success
     * @404 Resource not found
     */
    public function get($id)
    {
        $constituency = $this->constituencyService->get($this->params('constituency_id', null));
        return $constituency
            ? (new ItemModel($constituency))->setStatus(200)
            : (new ErrorModel('Resource not found'))->setStatus(404);
    }

    /**
     * @param Constituency $constituency
     * @return $this;
     */
    public function setConstituencyService(Constituency $constituency)
    {
        $this->constituencyService = $constituency;
        return $this;
    }
}
