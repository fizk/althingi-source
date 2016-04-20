<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Committee as CommitteeForm;
use Althingi\Lib\ServiceCommitteeAwareInterface;
use Althingi\Service\Committee;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;

class CommitteeController extends AbstractRestfulController implements
    ServiceCommitteeAwareInterface
{
    /** @var \Althingi\Service\Committee */
    private $committeeService;

    /**
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     */
    public function get($id)
    {
        $committee = $this->committeeService->get($id);

        if ($committee) {
            return new ItemModel($committee);

        }

        return $this->notFoundAction();
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     */
    public function getList()
    {
        $committees = $this->committeeService->fetchAll();

        return new CollectionModel($committees);
    }

    /**
     * Create new Resource Assembly.
     *
     * @param  int $id
     * @param  array $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function put($id, $data)
    {
        $form = new CommitteeForm();
        $form->bindValues(array_merge($data, ['assembly_id' => $id]));

        if ($form->isValid()) {
            $object = $form->getObject();
            $this->committeeService->create($object);
            return (new EmptyModel())
                ->setStatus(201);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * List options for Assembly collection.
     *
     * @return \Rend\View\Model\ModelInterface
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
     * @return \Rend\View\Model\ModelInterface
     */
    public function options()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['GET', 'OPTIONS', 'PUT', 'PATCH', 'DELETE'])
            ->setOption('Access-Control-Allow-Origin', '*');
    }

    /**
     * @param Committee $committee
     */
    public function setCommitteeService(Committee $committee)
    {
        $this->committeeService = $committee;
    }
}
