<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Assembly;
use Althingi\View\Model\ErrorModel;
use Althingi\View\Model\EmptyModel;
use Althingi\View\Model\ItemModel;
use Althingi\View\Model\CollectionModel;
use Zend\Form\FormInterface;
use Zend\Stdlib\ArrayObject;

class AssemblyController extends AbstractRestfulController
{
    use Range;

    /**
     * Return list of Assemblies.
     *
     * @return \Althingi\View\Model\CollectionModel
     */
    public function getList()
    {
        /** @var  $assemblyService \Althingi\Service\Assembly */
        $assemblyService = $this->getServiceLocator()
            ->get('Althingi\Service\Assembly');

        $order = $this->request->getQuery('order', 'desc');

        $count = $assemblyService->count();
        $range = $this->getRange($this->getRequest(), $count);
        $assemblies = $assemblyService->fetchAll(
            $range['from'],
            ($range['to']-$range['from']),
            $order
        );

        return (new CollectionModel($assemblies))
            ->setStatus(206)
            ->setRange($range['from'], $range['to'], $count);
    }

    /**
     * Create new Resource Assembly.
     *
     * @param  int $id
     * @param  array $data
     * @return \Althingi\View\Model\ItemModel
     */
    public function put($id, $data)
    {
        /** @var $assemblyService \Althingi\Service\Assembly */
        $sm = $this->getServiceLocator();

        $form = new Assembly();
        $form->bindValues(array_merge($data, ['assembly_id' => $id]));

        if ($form->isValid()) {
            $object = $form->getObject();
            $sm->get('Althingi\Service\Assembly')
                ->create($object);
            return (new EmptyModel())->setStatus(201);
        }
        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * List options for Assembly collection.
     *
     * @return \Althingi\View\Model\EmptyModel
     */
    public function optionsList()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['GET', 'OPTIONS'])
            ->setOption('Access-Control-Allow-Origin', '*');
    }

    /**
     * List options for Assembly entry.
     *
     * @return \Althingi\View\Model\EmptyModel
     */
    public function options()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['GET', 'OPTIONS', 'PUT', 'PATCH', 'DELETE'])
            ->setOption('Access-Control-Allow-Origin', '*');
    }

    /**
     * Get one Assembly.
     *
     * @param int $id
     * @return \Althingi\View\Model\ErrorModel|\Althingi\View\Model\ItemModel
     */
    public function get($id)
    {
        /** @var  $assemblyService \Althingi\Service\Assembly */
        $assemblyService = $this->getServiceLocator()
            ->get('Althingi\Service\Assembly');

        if (($resource = $assemblyService->get($id))) {
            return (new ItemModel($resource));
        }

        return $this->notFoundAction();
    }

    /**
     * Update one Assembly
     *
     * @param int $id
     * @param array $data
     * @return \Althingi\View\Model\ErrorModel|\Althingi\View\Model\EmptyModel
     */
    public function patch($id, $data)
    {
        /** @var $assemblyService \Althingi\Service\Assembly */
        $sm = $this->getServiceLocator();
        $assemblyService = $sm->get('Althingi\Service\Assembly');

        if (($assembly = $assemblyService->get($id)) != null) {
            $form = new Assembly();
            $form->bind($assembly);
            $form->setData($data);

            if ($form->isValid()) {
                $assemblyService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(204);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }

    /**
     * Delete one Assembly.
     *
     * @param int $id
     * @return \Althingi\View\Model\ErrorModel|\Althingi\View\Model\EmptyModel
     */
    public function delete($id)
    {
        $sm = $this->getServiceLocator();
        /** @var $assemblyService \Althingi\Service\Assembly */
        $assemblyService = $sm->get('Althingi\Service\Assembly');

        if (($assembly = $assemblyService->get($id)) != null) {
            $assemblyService->delete($id);
            return (new EmptyModel())->setStatus(200);
        }

        return $this->notFoundAction();
    }
}
