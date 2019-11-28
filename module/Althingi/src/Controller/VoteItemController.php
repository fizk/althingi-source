<?php

namespace Althingi\Controller;

use Althingi\Form;
use Althingi\Injector\ServiceConstituencyAwareInterface;
use Althingi\Model;
use Althingi\Injector\ServiceCongressmanAwareInterface;
use Althingi\Injector\ServicePartyAwareInterface;
use Althingi\Injector\ServiceVoteAwareInterface;
use Althingi\Injector\ServiceVoteItemAwareInterface;
use Althingi\Service\Congressman;
use Althingi\Service\Constituency;
use Althingi\Service\Party;
use Althingi\Service\Vote;
use Althingi\Service\VoteItem;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;

class VoteItemController extends AbstractRestfulController implements
    ServiceVoteItemAwareInterface,
    ServiceVoteAwareInterface,
    ServiceCongressmanAwareInterface,
    ServicePartyAwareInterface,
    ServiceConstituencyAwareInterface
{
    /** @var  \Althingi\Service\VoteItem */
    private $voteItemService;

    /** @var  \Althingi\Service\Vote */
    private $voteService;

    /** @var  \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var  \Althingi\Service\Party */
    private $partyService;

    /** @var  \Althingi\Service\Constituency */
    private $constituencyService;

    /**
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\VoteItem
     * @201 Created
     * @409 Conflict
     * @500 Service Error
     * @400 Invalid input
     */
    public function post($data)
    {
        $voteId = $this->params('vote_id');

        $form = new Form\VoteItem();
        $form->setData(array_merge($data, ['vote_id' => $voteId,]));

        if ($form->isValid()) {
            /** @var $formData \Althingi\Model\VoteItem*/
            $formData = $form->getObject();

            try {
                $this->voteItemService->create($formData);
                return (new EmptyModel())->setStatus(201);
            } catch (\PDOException $e) {
                if ($e->errorInfo[1] === 1062) {
                    $voteObject = $this->voteItemService->getByVote(
                        $formData->getVoteId(),
                        $formData->getCongressmanId()
                    );
                    return (new EmptyModel())
                        ->setLocation(
                            $this->url()->fromRoute(
                                'loggjafarthing/thingmal/atkvaedagreidslur/atkvaedagreidsla',
                                [
                                    'id' => $voteObject->getAssemblyId(),
                                    'issue_id' => $voteObject->getIssueId(),
                                    'vote_id' => $voteObject->getVoteId(),
                                    'vote_item_id' => $voteObject->getVoteItemId(),
                                    'category' => 'a'
                                ]
                            )
                        )
                        ->setStatus(409);
                } else {
                    return (new ErrorModel($e))
                        ->setStatus(500);
                }
            }
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\VoteItemAndCongressman[]
     * @206 Success
     */
    public function getList()
    {
        $vote = $this->voteService->get($this->params('vote_id'));

        $votes = $this->voteItemService->fetchByVote($vote->getVoteId());
        $date = $vote->getDate();

        $voteItems = array_map(function (Model\VoteItem $voteItem) use ($date) {
            $congressmanAndParty = (new Model\CongressmanPartyProperties())
                ->setCongressman($this->congressmanService->get(
                    $voteItem->getCongressmanId()
                ))->setParty($this->partyService->getByCongressman(
                    $voteItem->getCongressmanId(),
                    $date
                ))->setConstituency($this->constituencyService->getByCongressman(
                    $voteItem->getCongressmanId(),
                    $date
                ));

            return (new Model\VoteItemAndCongressman())
                ->setVoteItem($voteItem)
                ->setCongressman($congressmanAndParty);
        }, $votes);

        return (new CollectionModel($voteItems))
            ->setStatus(206)
            ->setRange(0, count($voteItems), count($voteItems));
    }

    /**
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\VoteItem
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch($id, $data)
    {
        $voteItemId = $this->params('vote_item_id');

        if (($voteItem = $this->voteItemService->get($voteItemId)) != null) {
            $form = new Form\VoteItem();
            $form->bind($voteItem);
            $form->setData($data);

            if ($form->isValid()) {
                $this->voteItemService->update($form->getData());
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
     * @param VoteItem $voteItem
     * @return $this
     */
    public function setVoteItemService(VoteItem $voteItem)
    {
        $this->voteItemService = $voteItem;
        return $this;
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
     * @param \Althingi\Service\Party $party
     * @return $this
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
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
