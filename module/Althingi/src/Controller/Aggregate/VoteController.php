<?php

namespace Althingi\Controller\Aggregate;

use Althingi\Injector\ServiceCongressmanAwareInterface;
use Althingi\Injector\ServiceConstituencyAwareInterface;
use Althingi\Injector\ServicePartyAwareInterface;
use Althingi\Injector\ServiceVoteAwareInterface;
use Althingi\Model\CongressmanAndParty;
use Althingi\Model\CongressmanPartyProperties;
use Althingi\Service\Congressman;
use Althingi\Service\Constituency;
use Althingi\Service\Party;
use Althingi\Service\Vote;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;
use DateTime;

class VoteController extends AbstractRestfulController implements
    ServiceVoteAwareInterface
{

    /** @var $issueService \Althingi\Service\Vote */
    private $voteService;

    /**
     * Get a single congressman.
     *
     * If date is provided, party and constituency are provided as well.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CongressmanPartyProperties | \Althingi\Model\Congressman
     * @query dags
     */
    public function getAction()
    {
        $id = $this->params('vote_id', null);

        return (new ItemModel($this->voteService->get($id)));
    }

    /**
     * @param \Althingi\Service\Vote $vote
     * @return $this
     */
    public function setVoteService(Vote $vote)
    {
        $this->voteService = $vote;
        return $this;
    }
}
