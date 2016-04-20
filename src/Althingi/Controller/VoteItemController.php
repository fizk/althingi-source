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
            $this->voteItemService->create($form->getObject());
            return (new EmptyModel())->setStatus(201);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * @param VoteItem $voteItem
     */
    public function setVoteItemService(VoteItem $voteItem)
    {
        $this->voteItemService = $voteItem;
    }
}
