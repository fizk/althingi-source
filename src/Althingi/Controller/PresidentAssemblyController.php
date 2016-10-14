<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Lib\ServiceCongressmanAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
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
     */
    public function getList()
    {
        $assemblyId = $this->params('id');
        $residents = $this->congressmanService->fetchPresidentsByAssembly($assemblyId);
        array_map(function ($president) {
            $president->party = $this->partyService
                ->getByCongressman($president->congressman_id, new \DateTime($president->from));
        }, $residents);

        return (new CollectionModel($residents))
            ->setOption('Access-Control-Allow-Origin', '*')
            ->setStatus(200);
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
