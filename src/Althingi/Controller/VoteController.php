<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 19/03/2016
 * Time: 12:20 PM
 */

namespace Althingi\Controller;

use Althingi\Form\Vote;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\ItemModel;

class VoteController extends AbstractRestfulController
{
    /**
     * Get one vote declaration.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     */
    public function get($id)
    {
        /** @var $assemblyService \Althingi\Service\Vote */
        $voteService = $this->getServiceLocator()
            ->get('Althingi\Service\Vote');

        if (($vote = $voteService->get($id)) != null) {
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

        /** @var  $voteService \Althingi\Service\Vote */
        $voteService = $this->getServiceLocator()
            ->get('Althingi\Service\Vote');

        return (new CollectionModel($voteService->fetchByIssue($assemblyId, $issueId)))
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

        /** @var  $voteService \Althingi\Service\Vote */
        $voteService = $this->getServiceLocator()
            ->get('Althingi\Service\Vote');

        $form = new Vote();
        $form->setData(array_merge($data, [
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'vote_id' => $voteId,
        ]));

        if ($form->isValid()) {
            $voteService->create($form->getObject());
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
        /** @var $assemblyService \Althingi\Service\Vote */
        $voteService = $this->getServiceLocator()
            ->get('Althingi\Service\Vote');

        if (($vote = $voteService->get($id)) != null) {
            $form = new Vote();
            $form->bind($vote);
            $form->setData($data);

            if ($form->isValid()) {
                $voteService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(204);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }

    /**
     * List options for Vote collection.
     *
     * @return \Rend\View\Model\EmptyModel
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
     * @return \Rend\View\Model\EmptyModel
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
}
