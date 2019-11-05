<?php

namespace Althingi\Controller\Aggregate;

use Althingi\Injector\ServiceConstituencyAwareInterface;
use Althingi\Service\Constituency;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ItemModel;
use Rend\Helper\Http\Range;

class ConstituencyController extends AbstractRestfulController implements
    ServiceConstituencyAwareInterface
{
    use Range;

    /** @var $issueService \Althingi\Service\Constituency */
    private $constituencyService;

    /**
     * @return ItemModel|\Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Constituency
     */
    public function get()
    {
        return (new ItemModel(
            $this->constituencyService->get($this->params('constituency_id', null))
        ));
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
