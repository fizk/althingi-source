<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Constituency;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\Helper\Http\Range;

class ConstituencyController extends AbstractRestfulController
{
    use Range;

    public function put($id, $data)
    {
        /** @var  $constituencyService \Althingi\Service\Constituency */
        $constituencyService = $this->getServiceLocator()
            ->get('Althingi\Service\Constituency');

        $form = new Constituency();
        $form->setData(array_merge($data, ['constituency_id' => $id]));
        if ($form->isValid()) {
            $constituencyService->create($form->getObject());
            return (new EmptyModel())
                ->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
    }
}
