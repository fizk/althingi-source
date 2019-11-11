<?php

namespace Althingi\Controller;

use Althingi\Injector\ServiceMinistryAwareInterface;
use Althingi\Service\Ministry;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;
use Althingi\Form;

class MinistryController extends AbstractRestfulController implements
    ServiceMinistryAwareInterface
{
    use Range;

    /** @var \Althingi\Service\Ministry */
    private $ministryService;

    /**
     * Get one Ministry.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface|array
     * @output \Althingi\Model\Ministry
     */
    public function get($id)
    {
        $ministry = $this->ministryService->get($id);
        return $ministry
            ? (new ItemModel($ministry))->setStatus(200)
            : (new EmptyModel('Resource Not Found'))->setStatus(404);
    }

    /**
     * Return list of Ministries.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Ministry[]
     */
    public function getList()
    {
        $ministries = $this->ministryService->fetchAll();

        return (new CollectionModel($ministries))
            ->setStatus(206)
            ->setRange(0, count($ministries), count($ministries));
    }

    /**
     * List options for Ministry collection.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function optionsList()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['GET', 'OPTIONS']);
    }

    /**
     * List options for Ministry entry.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function options()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['GET', 'OPTIONS', 'PUT', 'PATCH', 'DELETE']);
    }

    /**
     * Create new Resource Ministry.
     *
     * @param  int $id
     * @param  array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Ministry
     */
    public function put($id, $data)
    {
        $form = new Form\Ministry();
        $form->bindValues(array_merge($data, ['ministry_id' => $id]));

        if ($form->isValid()) {
            $object = $form->getObject();
            $affectedRows = $this->ministryService->save($object);
            return (new EmptyModel())
                ->setStatus($affectedRows === 1 ? 201 : 205);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * Update one Ministry
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Ministry
     */
    public function patch($id, $data)
    {
        if (($assembly = $this->ministryService->get($id)) != null) {
            $form = new Form\Ministry();
            $form->bind($assembly);
            $form->setData($data);

            if ($form->isValid()) {
                $this->ministryService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }

    /**
     * @param \Althingi\Service\Ministry $ministry
     * @return $this
     */
    public function setMinistryService(Ministry $ministry)
    {
        $this->ministryService = $ministry;
        return $this;
    }
}
