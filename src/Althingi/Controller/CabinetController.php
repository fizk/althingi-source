<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Assembly as AssemblyForm;
use Althingi\Lib\ServiceAssemblyAwareInterface;
use Althingi\Lib\ServiceCabinetAwareInterface;
use Althingi\Lib\ServiceCongressmanAwareInterface;
use Althingi\Lib\ServiceIssueAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Service\Assembly;
use Althingi\Service\Cabinet;
use Althingi\Service\Congressman;
use Althingi\Service\Issue;
use Althingi\Service\Party;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;

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

    public function assemblyAction()
    {
        $assemblyId = $this->params('id');

        $cabinets = array_map(function ($cabinet) {
            $cabinet->congressmen = array_map(function ($congressman) {
                $congressman->party = $this->partyService->getByCongressman(
                    $congressman->congressman_id,
                    new \DateTime($congressman->date)
                );
                return $congressman;
            }, $this->congressmanService->fetchByCabinet($cabinet->cabinet_id));
            return $cabinet;
        }, $this->cabinetService->fetchByAssembly($assemblyId));

        return (new CollectionModel($cabinets))
            ->setStatus(200);
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
