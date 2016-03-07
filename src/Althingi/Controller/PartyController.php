<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Party;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\Helper\Http\Range;

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
