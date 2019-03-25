<?php

namespace Althingi\Controller;

use Althingi\Form;
use Althingi\Injector\ServiceAssemblyAwareInterface;
use Althingi\Injector\ServiceCabinetAwareInterface;
use Althingi\Injector\ServiceInflationAwareInterface;
use Althingi\Service\Assembly;
use Althingi\Service\Cabinet;
use Althingi\Service\Inflation;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use DateTime;

class InflationController extends AbstractRestfulController implements
    ServiceInflationAwareInterface,
    ServiceCabinetAwareInterface,
    ServiceAssemblyAwareInterface
{
    /** @var \Althingi\Service\Inflation */
    private $inflationService;

    /** @var \Althingi\Service\Cabinet */
    private $cabinetService;

    /** @var \Althingi\Service\Assembly */
    private $assemblyService;

    /**
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Inflation
     */
    public function get($id)
    {
        $inflation = $this->inflationService->get($id);
        return $inflation
            ? new ItemModel($inflation)
            : $this->notFoundAction();
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Inflation[]
     * @query fra
     * @query til
     */
    public function getList()
    {
        $from = $this->params()->fromQuery('fra');
        $to = $this->params()->fromQuery('til');
        $assembly = $this->params()->fromQuery('loggjafarthing');

        if ($assembly) {
            $cabinet = $this->cabinetService->fetchByAssembly($assembly);
            if (count($cabinet) > 0) {
                $inflationCollection = $this->inflationService->fetchAll(
                    $cabinet[0]->getFrom(),
                    $cabinet[0]->getTo()
                );
                $inflationCollectionCount = count($inflationCollection);
                return (new CollectionModel($inflationCollection))
                    ->setStatus(206)
                    ->setRange(0, $inflationCollectionCount, $inflationCollectionCount);
            } else {
                return (new CollectionModel([]))
                    ->setStatus(206)
                    ->setRange(0, 0, 0);
            }
        } else {
            $inflationCollection = $this->inflationService->fetchAll(
                $from ? new DateTime($from) : null,
                $to ? new DateTime($to) : null
            );
            $inflationCollectionCount = count($inflationCollection);
            return (new CollectionModel($inflationCollection))
                ->setStatus(206)
                ->setRange(0, $inflationCollectionCount, $inflationCollectionCount);
        }
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Inflation[]
     */
    public function fetchAssemblyAction()
    {
        $assembly = $this->assemblyService->get($this->params('id'));
        $cabinet = $this->cabinetService->fetchByAssembly($assembly->getAssemblyId());

        if (count($cabinet) > 0) {
            $from = $assembly->getFrom() < $cabinet[0]->getFrom() ? $assembly->getFrom() : $cabinet[0]->getFrom();
            $to = $assembly->getTo() > $cabinet[0]->getTo() ? $assembly->getTo() : $cabinet[0]->getTo();

            $inflationCollection = $this->inflationService->fetchAll($from, $to);
            $inflationCollectionCount = count($inflationCollection);
            return (new CollectionModel($inflationCollection))
                ->setStatus(206)
                ->setRange(0, $inflationCollectionCount, $inflationCollectionCount);
        } else {
            return (new CollectionModel([]))
                ->setStatus(206)
                ->setRange(0, 0, 0);
        }
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
        $id = $this->params('id');

        $form = new Form\Inflation();
        $form->bindValues(array_merge($data, ['id' => $id]));

        if ($form->isValid()) {
            $affectedRows = $this->inflationService->save($form->getObject());
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
        if (($committee = $this->inflationService->get($id)) != null) {
            $form = new Form\Inflation();
            $form->bind($committee);
            $form->setData($data);

            if ($form->isValid()) {
                $this->inflationService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }

    /**
     * @param Inflation $inflation
     * @return $this
     */
    public function setInflationService(Inflation $inflation)
    {
        $this->inflationService = $inflation;
        return $this;
    }

    /**
     * @param Cabinet $cabinet
     * @return $this
     */
    public function setCabinetService(Cabinet $cabinet)
    {
        $this->cabinetService = $cabinet;
        return $this;
    }

    /**
     * @param \Althingi\Service\Assembly $assembly
     * @return $this
     */
    public function setAssemblyService(Assembly $assembly)
    {
        $this->assemblyService = $assembly;
        return $this;
    }
}
