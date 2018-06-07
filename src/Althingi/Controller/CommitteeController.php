<?php

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
     * @output \Althingi\Model\Committee
     */
    public function get($id)
    {
        $committee = $this->committeeService->get($id);

        return $committee
            ? new ItemModel($committee)
            : $this->notFoundAction();
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Committee[]
     */
    public function getList()
    {
        $committees = $this->committeeService->fetchAll();
        $committeesCount = count($committees);

        return (new CollectionModel($committees))
            ->setStatus(206)
            ->setRange(0, $committeesCount, $committeesCount);
    }

    /**
     * Create new Resource Assembly.
     *
     * @param  int $id
     * @param  array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Committee
     */
    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $committeeId = $this->params('committee_id');

        $form = new CommitteeForm();
        $form->bindValues(array_merge($data, ['assembly_id' => $assemblyId, 'committee_id' => $committeeId]));

        if ($form->isValid()) {
            $affectedRows = $this->committeeService->save($form->getObject());
            return (new EmptyModel())
                ->setStatus($affectedRows === 1 ? 201 : 205);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * @param  int $id
     * @param  array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Committee
     */
    public function patch($id, $data)
    {
        if (($committee = $this->committeeService->get($id)) != null) {
            $form = new CommitteeForm();
            $form->bind($committee);
            $form->setData($data);

            if ($form->isValid()) {
                $this->committeeService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
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
            ->setAllow(['GET', 'OPTIONS']);
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
            ->setAllow(['GET', 'OPTIONS', 'PUT', 'PATCH']);
    }

    /**
     * @param Committee $committee
     */
    public function setCommitteeService(Committee $committee)
    {
        $this->committeeService = $committee;
    }
}
