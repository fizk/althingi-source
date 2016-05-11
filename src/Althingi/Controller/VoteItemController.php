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
        $form = new VoteItemForm();
        $form->setData(array_merge($data, [
            'vote_id' => $this->params('vote_id'),
        ]));

        if ($form->isValid()) {
            $formData = $form->getObject();
//          TODO the VoteItem table has to have an auto_increment column
//            try {
                $this->voteItemService->create($formData);
                return (new EmptyModel())->setStatus(201);
//            } catch (\PDOException $e) {
//                if (23000 == $e->getCode()) {
//                    $voteObject =$this->voteItemService->getByVote($formData->vote_id, $formData->congressman_id);
//                    return (new EmptyModel())
//                        ->setLocation(
//                            $this->url()->fromRoute(
//                                'loggjafarthing/thingmal/atkvaedagreidslur/atkvaedagreidsla',
//                                [
//                                    'congressman_id' => $voteObject->congressman_id,
//                                    'id' => $voteObject->assembly_id,
//                                    'issue_id' => $voteObject->issue_id,
//                                    'vote_id' => $voteObject->vote_id
//                                ]
//                            )
//                        )
//                        ->setStatus(409);
//                } else {
//                    throw $e;
//                }
//            }
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

//    public function patch($id, $data)
//    {
//        $voteId = $this->params('vote_id');
//        $congressman_id = $this->params('congressman_id');
//
//        if (($voteItem = $this->voteItemService->get($voteId, $congressman_id)) != null) {
//            $form = new Issue();
//            $form->bind($voteItem);
//            $form->setData($data);
//
//            if ($form->isValid()) {
//                $this->voteItemService->update($form->getData());
//                return (new EmptyModel())
//                    ->setStatus(204)
//                    ->setOption('Access-Control-Allow-Origin', '*');
//            }
//
//            return (new ErrorModel($form))
//                ->setStatus(400)
//                ->setOption('Access-Control-Allow-Origin', '*');
//        }
//
//        return $this->notFoundAction();
//    }

    /**
     * @param VoteItem $voteItem
     */
    public function setVoteItemService(VoteItem $voteItem)
    {
        $this->voteItemService = $voteItem;
    }
}
