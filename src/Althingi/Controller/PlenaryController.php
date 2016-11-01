<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Plenary as PlenaryForm;
use Althingi\Lib\ServicePlenaryAwareInterface;
use Althingi\Service\Plenary;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;
use Rend\View\Model\ItemModel;

class PlenaryController extends AbstractRestfulController implements
    ServicePlenaryAwareInterface
{
    use Range;

    /** @var \Althingi\Service\Plenary */
    private $plenaryService;

    /**
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     */
    public function get($id)
    {
        $assemblyId = $this->params('id');
        $plenaryId = $this->params('plenary_id');

        if ($plenary = $this->plenaryService->get($assemblyId, $plenaryId)) {
            return new ItemModel($plenary);
        }

        return $this->notFoundAction();
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     */
    public function getList()
    {
        $assemblyId = $this->params('id', null);
        $count = $this->plenaryService->countByAssembly($assemblyId);
        $range = $this->getRange($this->getRequest(), $count);

        $plenaries = $this->plenaryService->fetchByAssembly(
            $assemblyId,
            $range['from'],
            ($range['to']-$range['from'])
        );
        return (new CollectionModel($plenaries))
            ->setStatus(206)
            ->setRange($range['from'], $range['to'], $count);
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function put($id, $data)
    {
        $form = (new PlenaryForm())
            ->setData(
                array_merge(
                    $data,
                    ['assembly_id' => $this->params('id'), 'plenary_id' => $id]
                )
            );

        if ($form->isValid()) {
            $this->plenaryService->create($form->getObject());
            return (new EmptyModel())->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function patch($id, $data)
    {
        $assemblyId = $this->params('id');
        $plenaryId = $this->params('plenary_id');

        if (($assembly = $this->plenaryService->get($assemblyId, $plenaryId)) != null) {
            $form = new PlenaryForm();
            $form->bind($assembly);
            $form->setData($data);

            if ($form->isValid()) {
                $this->plenaryService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }

    /**
     * @param Plenary $plenary
     */
    public function setPlenaryService(Plenary $plenary)
    {
        $this->plenaryService = $plenary;
    }
}
