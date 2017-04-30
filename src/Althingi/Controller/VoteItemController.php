<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 19/03/2016
 * Time: 12:20 PM
 */

namespace Althingi\Controller;

use Althingi\Form\VoteItem as VoteItemForm;
use Althingi\Lib\ServiceVoteItemAwareInterface;
use Althingi\Service\VoteItem;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;

class VoteItemController extends AbstractRestfulController implements
    ServiceVoteItemAwareInterface
{
    /** @var  \Althingi\Service\VoteItem */
    private $voteItemService;

    /**
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function post($data)
    {
        $voteId = $this->params('vote_id');

        $form = new VoteItemForm();
        $form->setData(array_merge($data, ['vote_id' => $voteId,]));

        if ($form->isValid()) {
            $formData = $form->getObject();

            try {
                $this->voteItemService->create($formData);
                return (new EmptyModel())->setStatus(201);
            } catch (\PDOException $e) {
                if (23000 == $e->getCode()) {
                    $voteObject =$this->voteItemService->getByVote($formData->vote_id, $formData->congressman_id);
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
                    throw $e;
                }
            }
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

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
}
