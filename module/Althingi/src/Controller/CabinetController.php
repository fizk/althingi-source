<?php

namespace Althingi\Controller;

use Althingi\Injector\ServiceAssemblyAwareInterface;
use Althingi\Injector\ServiceCabinetAwareInterface;
use Althingi\Injector\ServiceCongressmanAwareInterface;
use Althingi\Injector\ServicePartyAwareInterface;
use Althingi\Service;
use Althingi\Model;
use Althingi\Form;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;
use DateTime;

class CabinetController extends AbstractRestfulController implements
    ServiceCongressmanAwareInterface,
    ServicePartyAwareInterface,
    ServiceCabinetAwareInterface,
    ServiceAssemblyAwareInterface
{
    /** @var  \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var  \Althingi\Service\Party */
    private $partyService;

    /** @var  \Althingi\Service\Cabinet */
    private $cabinetService;

    /** @var  \Althingi\Service\Assembly */
    private $assemblyService;

    /**
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CabinetAndAssemblies
     */
    public function get($id)
    {
        $cabinet = $this->cabinetService->get($id);
        $assemblies = $this->assemblyService->fetchByCabinet($id);

        $cabinetAndAssembliesModel = (new Model\CabinetAndAssemblies())
            ->setCabinet($cabinet)
            ->setAssemblies($assemblies);

        return $cabinet
            ? new ItemModel($cabinetAndAssembliesModel)
            : $this->notFoundAction();
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Cabinet[]
     * @query fra
     * @query til
     */
    public function getList()
    {
        $from = $this->params()->fromQuery('fra');
        $to = $this->params()->fromQuery('til');

        $cabinetCollection = $this->cabinetService->fetchAll(
            $from ? new DateTime($from) : null,
            $to ? new DateTime($to) : null
        );
        $cabinetCollectionCount = count($cabinetCollection);

        return (new CollectionModel($cabinetCollection))
            ->setStatus(206)
            ->setRange(0, $cabinetCollectionCount, $cabinetCollectionCount);
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

        $form = new Form\Cabinet();
        $form->bindValues(array_merge($data, ['cabinet_id' => $id]));

        if ($form->isValid()) {
            $affectedRows = $this->cabinetService->save($form->getObject());
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
        if (($committee = $this->cabinetService->get($id)) != null) {
            $form = new Form\Cabinet();
            $form->bind($committee);
            $form->setData($data);

            if ($form->isValid()) {
                $this->cabinetService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }

    /**
     * @return CollectionModel
     * $output \Althingi\Model\CabinetProperties[]
     */
    public function assemblyAction()
    {
        $assemblyId = $this->params('id');
        $assembly = $this->assemblyService->get($assemblyId);

        $cabinets = $this->cabinetService->fetchAll(
            $assembly->getFrom(),
            $assembly->getTo()
        );

        return new CollectionModel($cabinets);
    }

    /**
     * @param \Althingi\Service\Congressman $congressman
     * @return $this
     */
    public function setCongressmanService(Service\Congressman $congressman)
    {
        $this->congressmanService = $congressman;
        return $this;
    }

    /**
     * @param \Althingi\Service\Party $party
     * @return $this
     */
    public function setPartyService(Service\Party $party)
    {
        $this->partyService = $party;
        return $this;
    }

    /**
     * @param \Althingi\Service\Cabinet $cabinet
     * @return $this
     */
    public function setCabinetService(Service\Cabinet $cabinet)
    {
        $this->cabinetService = $cabinet;
        return $this;
    }

    /**
     * @param \Althingi\Service\Assembly $assembly
     * @return $this
     */
    public function setAssemblyService(Service\Assembly $assembly)
    {
        $this->assemblyService = $assembly;
        return $this;
    }
}
