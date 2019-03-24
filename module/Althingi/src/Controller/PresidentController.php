<?php

namespace Althingi\Controller;

use Althingi\Injector\ServiceCongressmanAwareInterface;
use Althingi\Injector\ServicePartyAwareInterface;
use Althingi\Injector\ServicePresidentAwareInterface;
use Althingi\Model\CongressmanPartyProperties;
use Althingi\Model\PresidentPartyProperties;
use Althingi\Service\Congressman;
use Althingi\Service\Party;
use Althingi\Service\President;
use Althingi\Form;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\ItemModel;

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
     * @output \Althingi\Model\CongressmanPartyProperties
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
     * @output \Althingi\Model\PresidentPartyProperties[]
     */
    public function getList()
    {
        $presidents = $this->congressmanService->fetchPresidents();
        $presidentsAndParties = array_map(function (\Althingi\Model\President $president) {
            return (new PresidentPartyProperties)
                ->setPresident($president)
                ->setParty($this->partyService->getByCongressman(
                    $president->getCongressmanId(),
                    $president->getFrom()
                ));
        }, $presidents);
        $presidentsAndPartiesCount = count($presidentsAndParties);

        return (new CollectionModel($presidentsAndParties))
            ->setStatus(206)
            ->setRange(0, $presidentsAndPartiesCount, $presidentsAndPartiesCount);
    }

    /**
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\President
     */
    public function post($data)
    {
        $form = new Form\President();
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
     * @input \Althingi\Form\President
     */
    public function patch($id, $data)
    {
        if (($president = $this->presidentService->get($id)) != null) {
            $form = new Form\President();
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
     * @return $this
     */
    public function setPresidentService(President $president)
    {
        $this->presidentService = $president;
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
     * @param Congressman $congressman
     * @return $this
     */
    public function setCongressmanService(Congressman $congressman)
    {
        $this->congressmanService = $congressman;
        return $this;
    }
}
