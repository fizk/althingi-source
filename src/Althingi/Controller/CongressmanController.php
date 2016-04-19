<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 8/06/15
 * Time: 9:05 PM
 */

namespace Althingi\Controller;

use Althingi\Form\Congressman;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;

class CongressmanController extends AbstractRestfulController
{
    use Range;

    /**
     * Return list of congressmen.
     *
     * @return \Rend\View\Model\CollectionModel
     */
    public function getList()
    {
        /** @var  $assemblyService \Althingi\Service\Congressman */
        $congressmanService = $this->getServiceLocator()
            ->get('Althingi\Service\Congressman');

        $count = $congressmanService->count();
        $range = $this->getRange($this->getRequest(), $count);
        $assemblies = $congressmanService->fetchAll($range['from'], $range['to']);

        return (new CollectionModel($assemblies))
            ->setStatus(206)
            ->setRange($range['from'], $range['to'], $count);
    }

    /**
     * Get one congressman.
     *
     * @param int $id
     * @return \Rend\View\Model\ItemModel
     */
    public function get($id)
    {
        /** @var  $assemblyService \Althingi\Service\Congressman */
        $congressmanService = $this->getServiceLocator()
            ->get('Althingi\Service\Congressman');

        if ($congressman = $congressmanService->get($id)) {
            return (new ItemModel($congressman))
                ->setOption('Access-Control-Allow-Origin', '*');
        }

        return $this->notFoundAction();
    }

    /**
     * Create on congressman entry.
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\EmptyModel|\Rend\View\Model\ErrorModel
     */
    public function put($id, $data)
    {
        /** @var  $congressmanService \Althingi\Service\Congressman */
        $congressmanService = $this->getServiceLocator()
            ->get('Althingi\Service\Congressman');

        $form = new Congressman();
        $form->setData(array_merge($data, ['congressman_id' => $id]));

        if ($form->isValid()) {
            $congressmanService->create($form->getObject());
            return (new EmptyModel())->setStatus(201);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * Update congressman.
     *
     * @param $id
     * @param $data
     * @return EmptyModel|ErrorModel
     */
    public function patch($id, $data)
    {
        $congressmanService = $this->getServiceLocator()
            ->get('Althingi\Service\Congressman');
        $congressman = $congressmanService->get($id);

        if (!$congressman) {
            return $this->notFoundAction();
        }

        $form = (new Congressman())
            ->bind($congressman)
            ->setData($data);

        if ($form->isValid()) {
            $congressmanService->update($form->getObject());
            return (new EmptyModel())->setStatus(204);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * Create new Congressman allowing the system
     * to auto-generate the ID.
     *
     * @param mixed $data
     * @return $this
     */
    public function create($data)
    {
        /** @var  $congressmanService \Althingi\Service\Congressman */
        $congressmanService = $this->getServiceLocator()
            ->get('Althingi\Service\Congressman');

        $form = (new Congressman())
            ->setData($data);

        if ($form->isValid()) {
            $id = $congressmanService->create($form->getObject());
            return (new EmptyModel())
                ->setLocation($this->url()->fromRoute('thingmenn', ['id' => $id]))
                ->setStatus(201);
        }
        return (new ErrorModel($form))
            ->setStatus(400);
    }


    public function delete($id)
    {
        /** @var  $congressmanService \Althingi\Service\Congressman */
        $congressmanService = $this->getServiceLocator()
            ->get('Althingi\Service\Congressman');
        $congressmanService->delete($id);

        return (new EmptyModel())->setStatus(204);
    }
}
