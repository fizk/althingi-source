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
use Althingi\Lib\ServicePresidentAwareInterface;
use Althingi\Model\CongressmanPartyProperties;
use Althingi\Model\PresidentPartyProperties;
use Althingi\Service\Congressman;
use Althingi\Service\Party;
use Althingi\Service\President;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\ItemModel;
use Althingi\Form\President as PresidentForm;

class PresidentController extends AbstractRestfulController implements
    ServicePresidentAwareInterface,
    ServicePartyAwareInterface,
    ServiceCongressmanAwareInterface
{
    /** @var $presidentService \Althingi\Service\President */
    private $presidentService;

    /** @var $presidentService \Althingi\Service\Party */
    private $partyService;

    /** @var $presidentService \Althingi\Service\Congressman */
    private $congressmanService;

    /**
     * Return one Presidents.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     */
    public function get($id)
    {
        $president = $this->presidentService->getWithCongressman($id);

        if ($president) {
            $congressmanPartyProperties = (new CongressmanPartyProperties())
                ->setCongressman($president)
                ->setParty(
                    $this->partyService->getByCongressman($president->getCongressmanId(), $president->getFrom())
                );

            return (new ItemModel($congressmanPartyProperties))->setStatus(200);
        }

        return $this->notFoundAction();
    }

    /**
     * Return list of Presidents.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function getList()
    {
        $presidents = $this->congressmanService->fetchPresidents();
        $presidentsAndParties = array_map(function (\Althingi\Model\President $president) {
            $p = (new PresidentPartyProperties)
                ->setPresident($president)
                ->setParty($this->partyService->getByCongressman(
                    $president->getCongressmanId(),
                    $president->getFrom()
                ));
            return $p;
        }, $presidents);

        return (new CollectionModel($presidentsAndParties))
            ->setStatus(200);
    }

    /**
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function post($data)
    {
        $form = new PresidentForm();
        $form->bindValues($data);

        if ($form->isValid()) {
            /** @var $newPresident \Althingi\Model\President */
            $newPresident = $form->getObject();
            $statusCode = 201;
            $presidentId = 0;

            try {
                $presidentId = $this->presidentService->create($newPresident);
            } catch (\Exception $e) {
                if ($e->getCode() == 23000) {
                    $existingPresident = $this->presidentService->getByUnique(
                        $newPresident->getAssemblyId(),
                        $newPresident->getCongressmanId(),
                        $newPresident->getFrom(),
                        $newPresident->getTitle()
                    );
                    $presidentId = $existingPresident->getPresidentId();
                    $statusCode = 409;
                }
            }

            return (new EmptyModel())
                ->setLocation($this->url()->fromRoute('forsetar', ['id' => $presidentId]))
                ->setStatus($statusCode);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function patch($id, $data)
    {
        if (($president = $this->presidentService->get($id)) != null) {
            $form = new PresidentForm();
            $form->bind($president);
            $form->setData($data);

            if ($form->isValid()) {
                $this->presidentService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
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

    /**
     * @param Congressman $congressman
     */
    public function setCongressmanService(Congressman $congressman)
    {
        $this->congressmanService = $congressman;
    }
}
