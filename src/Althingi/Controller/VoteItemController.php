<?php

namespace Althingi\Controller;

use Althingi\Form\VoteItem as VoteItemForm;
use Althingi\Lib\ServiceCongressmanAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Lib\ServiceVoteAwareInterface;
use Althingi\Lib\ServiceVoteItemAwareInterface;
use Althingi\Model\CongressmanPartyProperties;
use Althingi\Model\VoteItemAndCongressman;
use Althingi\Service\Congressman;
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
    ServicePartyAwareInterface
{
    /** @var  \Althingi\Service\VoteItem */
    private $voteItemService;

    /** @var  \Althingi\Service\Vote */
    private $voteService;

    /** @var  \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var  \Althingi\Service\Party */
    private $partyService;

    /**
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\VoteItem
     */
    public function post($data)
    {
        $voteId = $this->params('vote_id');

        $form = new VoteItemForm();
        $form->setData(array_merge($data, ['vote_id' => $voteId,]));

        if ($form->isValid()) {
            /** @var $formData \Althingi\Model\VoteItem*/
            $formData = $form->getObject();

            try {
                $this->voteItemService->create($formData);
                return (new EmptyModel())->setStatus(201);
            } catch (\PDOException $e) {
                if (23000 == $e->getCode()) {
                    $voteObject =$this->voteItemService->getByVote(
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
                                    'vote_item_id' => $voteObject->getVoteItemId()
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
     */
    public function getList()
    {
        $vote = $this->voteService->get($this->params('vote_id'));

        $votes = $this->voteItemService->fetchByVote($vote->getVoteId());
        $date = $vote->getDate();

        $voteItems = array_map(function (\Althingi\Model\VoteItem $voteItem) use ($date) {
            $congressman = $this->congressmanService->get($voteItem->getCongressmanId());
            $party = $this->partyService->getByCongressman($voteItem->getCongressmanId(), $date);

            $congressmanAndParty = new CongressmanPartyProperties();
            $congressmanAndParty->setCongressman($congressman);
            $congressmanAndParty->setParty($party);

            $voteItemAndCongressman = new VoteItemAndCongressman();
            $voteItemAndCongressman->setVoteItem($voteItem);
            $voteItemAndCongressman->setCongressman($congressmanAndParty);

            return $voteItemAndCongressman;
        }, $votes);
        $voteItemsCount = count($voteItems);


        return (new CollectionModel($voteItems))
            ->setStatus(206)
            ->setRange(0, $voteItemsCount, $voteItemsCount);
    }

    /**
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\VoteItem
     */
    public function patch($id, $data)
    {
        $voteItemId = $this->params('vote_item_id');

        if (($voteItem = $this->voteItemService->get($voteItemId)) != null) {
            $form = new VoteItemForm();
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

        return $this->notFoundAction();
    }

    /**
     * @param VoteItem $voteItem
     */
    public function setVoteItemService(VoteItem $voteItem)
    {
        $this->voteItemService = $voteItem;
    }

    /**
     * @param \Althingi\Service\Vote $vote
     */
    public function setVoteService(Vote $vote)
    {
        $this->voteService = $vote;
    }

    /**
     * @param Congressman $congressman
     */
    public function setCongressmanService(Congressman $congressman)
    {
        $this->congressmanService = $congressman;
    }

    /**
     * @param \Althingi\Service\Party $party
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
    }
}
