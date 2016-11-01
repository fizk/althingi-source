<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 19/03/2016
 * Time: 12:20 PM
 */

namespace Althingi\Controller;

use Althingi\Form\Vote as VoteForm;
use Althingi\Lib\ServiceVoteAwareInterface;
use Althingi\Service\Vote;
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
     *
     * @attr int id
     * @attr int issue_id
     */
    public function getList()
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');

        return (new CollectionModel($this->voteService->fetchByIssue($assemblyId, $issueId)))
            ->setOption('Access-Control-Allow-Origin', '*');
    }

    /**
     * Create/PUT a new vote item.
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
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

        $form = new VoteForm();
        $form->setData(array_merge($data, [
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'vote_id' => $voteId,
        ]));

        if ($form->isValid()) {
            $this->voteService->create($form->getObject());
            return (new EmptyModel())
                ->setStatus(201);
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
     *
     * @attr int id
     * @attr int issue_id
     * @attr int vote_id
     */
    public function patch($id, $data)
    {
        if (($vote = $this->voteService->get($id)) != null) {
            $form = new VoteForm();
            $form->bind($vote);
            $form->setData($data);

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
            ->setAllow(['GET', 'OPTIONS'])
            ->setOption('Access-Control-Allow-Origin', '*');
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
            ->setAllow(['OPTIONS', 'PUT', 'PATCH', 'GET'])
            ->setOption('Access-Control-Allow-Origin', '*');
    }

    /**
     * @param Vote $vote
     */
    public function setVoteService(Vote $vote)
    {
        $this->voteService = $vote;
    }
}
