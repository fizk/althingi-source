<?php

namespace Althingi\Controller;

use Althingi\Lib\ServiceCongressmanAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Model\President;
use Althingi\Model\PresidentPartyProperties;
use Althingi\Service\Congressman;
use Althingi\Service\Party;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;

class PresidentAssemblyController extends AbstractRestfulController implements
    ServicePartyAwareInterface,
    ServiceCongressmanAwareInterface
{

    /** @var $presidentService \Althingi\Service\Party */
    private $partyService;

    /** @var $presidentService \Althingi\Service\Congressman */
    private $congressmanService;

    /**
     * Return list of Assemblies.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\PresidentPartyProperties[]
     */
    public function getList()
    {
        $assemblyId = $this->params('id');
        $residents = $this->congressmanService->fetchPresidentsByAssembly($assemblyId);
        array_map(function (President $president) {
            $congressmanAndParty = new PresidentPartyProperties();
            $congressmanAndParty
                ->setPresident($president)
                ->setParty(
                    $this->partyService->getByCongressman($president->getPresidentId(), $president->getFrom())
                );
        }, $residents);

        return (new CollectionModel($residents))
            ->setStatus(206)
            ->setRange(0, count($residents), count($residents));
    }

    /**
     * @param Party $party
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
    }

    /**
     * @param Congressman $congressman
     */
    public function setCongressmanService(Congressman $congressman)
    {
        $this->congressmanService = $congressman;
    }
}
