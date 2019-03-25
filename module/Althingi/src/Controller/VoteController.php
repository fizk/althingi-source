<?php

namespace Althingi\Controller;

use Althingi\Form;
use Althingi\Service\Vote;
use Althingi\Injector\ServiceVoteAwareInterface;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\ItemModel;

class VoteController extends AbstractRestfulController implements
    ServiceVoteAwareInterface
{
    /** @var \Althingi\Service\Vote */
    private $voteService;

    /**
     * Get one vote declaration.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Vote
     */
    public function get($id)
    {
        if (($vote = $this->voteService->get($id)) != null) {
            return (new ItemModel($vote));
        }

        return $this->notFoundAction();
    }

    /**
     * Get a list of votes for a given issue.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Vote[]
     *
     * @attr int id
     * @attr int issue_id
     */
    public function getList()
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $issues = $this->voteService->fetchByIssue($assemblyId, $issueId);
        $issuesCount = count($issues);

        return (new CollectionModel($issues))
            ->setStatus(206)
            ->setRange(0, $issuesCount, $issuesCount);
    }

    /**
     * Create/PUT a new vote item.
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Vote
     *
     * @attr int id
     * @attr int issue_id
     * @attr int vote_id
     */
    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $voteId = $id;

        $form = new Form\Vote();
        $form->setData(array_merge($data, [
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'vote_id' => $voteId,
            'category' => 'A'
        ]));

        if ($form->isValid()) {
            $affectedRows = $this->voteService->save($form->getObject());
            return (new EmptyModel())
                ->setStatus($affectedRows === 1 ? 201 : 205);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * Update/PATCH a vote entry.
     *
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Vote
     *
     * @attr int id
     * @attr int issue_id
     * @attr int vote_id
     */
    public function patch($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $voteId = $id;

        if (($vote = $this->voteService->get($id)) != null) {
            $form = new Form\Vote();
            $form->bind($vote);
            $form->setData(array_merge($data, [
                'assembly_id' => $assemblyId,
                'issue_id' => $issueId,
                'vote_id' => $voteId,
            ]));

            if ($form->isValid()) {
                $this->voteService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }

    /**
     * List options for Vote collection.
     *
     * @return \Rend\View\Model\ModelInterface
     *
     * @attr int id
     * @attr int issue_id
     */
    public function optionsList()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['GET', 'OPTIONS']);
    }

    /**
     * List options for Vote entry.
     *
     * @return \Rend\View\Model\ModelInterface
     *
     * @attr int id
     * @attr int issue_id
     * @attr int vote_id
     */
    public function options()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['OPTIONS', 'PUT', 'PATCH', 'GET']);
    }

    /**
     * @param Vote $vote
     * @return $this
     */
    public function setVoteService(Vote $vote)
    {
        $this->voteService = $vote;
        return $this;
    }
}
