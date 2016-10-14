<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Lib\ServicePresidentAwareInterface;
use Althingi\Service\Party;
use Althingi\Service\President;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;

class PresidentAssemblyController extends AbstractRestfulController implements
    ServicePresidentAwareInterface,
    ServicePartyAwareInterface
{
    /** @var $presidentService \Althingi\Service\President */
    private $presidentService;

    /** @var $presidentService \Althingi\Service\Party */
    private $partyService;

    /**
     * Return list of Assemblies.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function getList()
    {
        $assemblyId = $this->params('id');
        $residents = $this->presidentService->fetchAssembly($assemblyId);
        array_map(function ($president) {
            $president->party = $this->partyService
                ->getByCongressman($president->congressman_id, new \DateTime($president->from));
        }, $residents);

        return (new CollectionModel($residents))
            ->setOption('Access-Control-Allow-Origin', '*')
            ->setStatus(200);
    }

    /**
     * @param President $president
     */
    public function setPresidentService(President $president)
    {
        $this->presidentService = $president;
    }

    /**
     * @param Party $party
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
    }
}
