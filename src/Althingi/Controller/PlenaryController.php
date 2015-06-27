<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Plenary;
use Althingi\View\Model\ErrorModel;
use Althingi\View\Model\EmptyModel;
use Althingi\View\Model\CollectionModel;

class PlenaryController extends AbstractRestfulController
{
    use Range;

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
}
