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
     * @200 Success
     * @404 Resource not found
     */
    public function get($id)
    {
        $vote = $this->voteService->get($id);
        return $vote
            ? (new ItemModel($vote))->setStatus(200)
            : (new ErrorModel('Resource Not Found'))->setStatus(404);
    }

    /**
     * Get a list of votes for a given issue.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Vote[]
     * @206 Success
     */
    public function getList()
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $issues = $this->voteService->fetchByIssue($assemblyId, $issueId);

        return (new CollectionModel($issues))
            ->setStatus(206)
            ->setRange(0, count($issues), count($issues));
    }

    /**
     * Create/PUT a new vote item.
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Vote
     * @201 Created
     * @205 Updated
     * @400 Invalid input
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
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
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

        return (new ErrorModel('Resource Not Found'))
            ->setStatus(404);
    }

    /**
     * List options for Vote collection.
     *
     * @return \Rend\View\Model\ModelInterface
     * @200 Success
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
     * @200 Success
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
