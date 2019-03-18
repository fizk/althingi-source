<?php

namespace Althingi\Controller;

use Althingi\Lib\ServiceAssemblyAwareInterface;
use Althingi\Lib\ServiceCabinetAwareInterface;
use Althingi\Lib\ServiceCongressmanAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Model\CabinetAndAssemblies;
use Althingi\Service\Assembly;
use Althingi\Service\Cabinet;
use Althingi\Service\Congressman;
use Althingi\Service\Party;
use Althingi\Form\Cabinet as CabinetForm;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;

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

        $cabinetAndAssembliesModel = (new CabinetAndAssemblies())
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
            $from ? new \DateTime($from) : null,
            $to ? new \DateTime($to) : null
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

        $form = new CabinetForm();
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
            $form = new CabinetForm();
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
     * @param Congressman $congressman
     * @return $this
     */
    public function setCongressmanService(Congressman $congressman)
    {
        $this->congressmanService = $congressman;
        return $this;
    }

    /**
     * @param Party $party
     * @return $this
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
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
