<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Committee;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;

class CommitteeController extends AbstractRestfulController
{
    public function get($id)
    {
        /** @var $committeeService \Althingi\Service\Committee */
        $committeeService = $this->getServiceLocator()
            ->get('Althingi\Service\Committee');

        $committee = $committeeService->get($id);

        if ($committee) {
            return new ItemModel($committee);

        }

        return $this->notFoundAction();
    }

    public function getList()
    {
        /** @var $committeeService \Althingi\Service\Committee */
        $committeeService = $this->getServiceLocator()
            ->get('Althingi\Service\Committee');

        $committees = $committeeService->fetchAll();

        return new CollectionModel($committees);
    }

    /**
     * Create new Resource Assembly.
     *
     * @param  int $id
     * @param  array $data
     * @return \Rend\View\Model\ItemModel
     */
    public function put($id, $data)
    {
        /** @var $committeeService \Althingi\Service\Committee */
        $committeeService = $this->getServiceLocator()
            ->get('Althingi\Service\Committee');

        $form = new Committee();
        $form->bindValues(array_merge($data, ['assembly_id' => $id]));

        if ($form->isValid()) {
            $object = $form->getObject();
            $committeeService->create($object);
            return (new EmptyModel())
                ->setStatus(201);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * List options for Assembly collection.
     *
     * @return \Rend\View\Model\EmptyModel
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
     * @return \Rend\View\Model\EmptyModel
     */
    public function options()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['GET', 'OPTIONS', 'PUT', 'PATCH', 'DELETE'])
            ->setOption('Access-Control-Allow-Origin', '*');
    }
}
