<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 19/03/2016
 * Time: 12:20 PM
 */

namespace Althingi\Controller;

use Althingi\Form\VoteItem;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;

class VoteItemController extends AbstractRestfulController
{
    public function post($data)
    {

        /** @var  $congressmanService \Althingi\Service\VoteItem */
        $voteItemService = $this->getServiceLocator()
            ->get('Althingi\Service\VoteItem');

        $form = new VoteItem();
        $form->setData(array_merge($data, [
            'vote_id' => $this->params('vote_id'),
        ]));

        if ($form->isValid()) {
            $voteItemService->create($form->getObject());
            return (new EmptyModel())->setStatus(201);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }
}
