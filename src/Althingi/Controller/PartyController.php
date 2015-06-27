<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Assembly;
use Althingi\Form\Party;
use Althingi\View\Model\ErrorModel;
use Althingi\View\Model\EmptyModel;
use Althingi\View\Model\ItemModel;
use Althingi\View\Model\CollectionModel;

class PartyController extends AbstractRestfulController
{
    use Range;

    public function put($id, $data)
    {
        /** @var  $partyService \Althingi\Service\Party */
        $partyService = $this->getServiceLocator()
            ->get('Althingi\Service\Party');

        $form = new Party();
        $form->bindValues(array_merge($data, ['party_id' => $id]));
        if ($form->isValid()) {
            $partyService->create($form->getObject());
            return (new EmptyModel())
                ->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
    }
}
