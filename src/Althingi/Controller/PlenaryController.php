<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Plenary;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;
use Rend\View\Model\ItemModel;

class PlenaryController extends AbstractRestfulController
{
    use Range;

    public function get($id)
    {
        $assemblyId = $this->params('id');
        $plenaryId = $this->params('plenary_id');

        /** @var $plenaryService \Althingi\Service\Plenary */
        $sm = $this->getServiceLocator();
        $plenaryService = $sm->get('Althingi\Service\Plenary');

        if ($plenary = $plenaryService->get($assemblyId, $plenaryId)) {
            return new ItemModel($plenary);
        }

        return $this->notFoundAction();
    }

    public function getList()
    {
        /** @var  $plenaryService \Althingi\Service\Plenary*/
        $plenaryService = $this->getServiceLocator()
            ->get('Althingi\Service\Plenary');

        $assemblyId = $this->params('id', null);
        $count = $plenaryService->countByAssembly($assemblyId);
        $range = $this->getRange($this->getRequest(), $count);

        $plenaries = $plenaryService->fetchByAssembly(
            $assemblyId,
            $range['from'],
            ($range['to']-$range['from'])
        );
        return (new CollectionModel($plenaries))
            ->setStatus(206)
            ->setRange($range['from'], $range['to'], $count);
    }

    public function put($id, $data)
    {
        $plenaryService = $this->getServiceLocator()
            ->get('Althingi\Service\Plenary');

        $form = (new Plenary())
            ->setData(
                array_merge(
                    $data,
                    ['assembly_id' => $this->params('id'), 'plenary_id' => $id]
                )
            );

        if ($form->isValid()) {
            $plenaryService->create($form->getObject());
            return (new EmptyModel())->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    public function patch($id, $data)
    {
        $assemblyId = $this->params('id');
        $plenaryId = $this->params('plenary_id');

        /** @var $plenaryService \Althingi\Service\Plenary */
        $sm = $this->getServiceLocator();
        $plenaryService = $sm->get('Althingi\Service\Plenary');

        if (($assembly = $plenaryService->get($assemblyId, $plenaryId)) != null) {
            $form = new Plenary();
            $form->bind($assembly);
            $form->setData($data);

            if ($form->isValid()) {
                $plenaryService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(204);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }
}
