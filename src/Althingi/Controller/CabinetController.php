<?php

namespace Althingi\Controller;

use Althingi\Lib\ServiceCabinetAwareInterface;
use Althingi\Lib\ServiceCongressmanAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Model\CabinetProperties;
use Althingi\Model\CongressmanAndCabinet;
use Althingi\Model\CongressmanPartyProperties;
use Althingi\Service\Cabinet;
use Althingi\Service\Congressman;
use Althingi\Service\Party;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;

class CabinetController extends AbstractRestfulController implements
    ServiceCongressmanAwareInterface,
    ServicePartyAwareInterface,
    ServiceCabinetAwareInterface
{
    /** @var  \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var  \Althingi\Service\Party */
    private $partyService;

    /** @var  \Althingi\Service\Cabinet */
    private $cabinetService;

    /**
     * @return CollectionModel
     * $output \Althingi\Model\CabinetProperties[]
     */
    public function assemblyAction()
    {
        $assemblyId = $this->params('id');

        $cabinetsCollection = array_map(function (\Althingi\Model\Cabinet $cabinet) {
            $congressmenParty = array_map(function (CongressmanAndCabinet $congressman) {
                return (new CongressmanPartyProperties())
                    ->setCongressman($congressman)
                    ->setParty(
                        $this->partyService->getByCongressman(
                            $congressman->getCongressmanId(),
                            $congressman->getDate()
                        )
                    );
            }, $this->congressmanService->fetchByCabinet($cabinet->getCabinetId()));

            return (new CabinetProperties())
                ->setCabinet($cabinet)
                ->setCongressmen($congressmenParty);
        }, $this->cabinetService->fetchByAssembly($assemblyId));

        return new CollectionModel($cabinetsCollection);
    }

    /**
     * @param Congressman $congressman
     */
    public function setCongressmanService(Congressman $congressman)
    {
        $this->congressmanService = $congressman;
    }

    /**
     * @param Party $party
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
    }

    /**
     * @param Cabinet $cabinet
     */
    public function setCabinetService(Cabinet $cabinet)
    {
        $this->cabinetService = $cabinet;
    }
}
