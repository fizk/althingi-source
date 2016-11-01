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
        $president = $this->presidentService->get($id);

        if ($president) {
            $president->party = $this->partyService
                ->getByCongressman($president->congressman_id, new \DateTime($president->from));
        }

        return (new ItemModel($president))
            ->setOption('Access-Control-Allow-Origin', '*')
            ->setStatus(200);
    }

    /**
     * Return list of Presidents.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function getList()
    {
        $residents = $this->congressmanService->fetchPresidents();
        array_map(function ($president) {
            $president->party = $this->partyService
                ->getByCongressman($president->congressman_id, new \DateTime($president->from));
        }, $residents);

        return (new CollectionModel($residents))
            ->setOption('Access-Control-Allow-Origin', '*')
            ->setStatus(200);
    }

    public function post($data)
    {
        $form = new PresidentForm();
        $form->bindValues($data);

        if ($form->isValid()) {
            $object = $form->getObject();
            $statusCode = 201;
            $presidentId = 0;

            try {
                $presidentId = $this->presidentService->create($object);
            } catch (\Exception $e) {
                if ($e->getCode() == 23000) {
                    $entry = $this->presidentService->getByUnique(
                        $object->assembly_id,
                        $object->congressman_id,
                        new \DateTime($object->from),
                        $object->title
                    );
                    $presidentId = $entry->president_id;
                    $statusCode = 409;
                }
            }

            return (new EmptyModel())
                ->setLocation($this->url()->fromRoute('forsetar', ['id' => $presidentId]))
                ->setStatus($statusCode)
                ->setOption('Access-Control-Allow-Origin', '*');
        }

        return (new ErrorModel($form))
            ->setStatus(400)
            ->setOption('Access-Control-Allow-Origin', '*');
    }

    public function patch($id, $data)
    {
        if (($president = $this->presidentService->get($id)) != null) {
            $form = new PresidentForm();
            $form->bind($president);
            $form->setData($data);

            if ($form->isValid()) {
                $this->presidentService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205)
                    ->setOption('Access-Control-Allow-Origin', '*');
            }

            return (new ErrorModel($form))
                ->setStatus(400)
                ->setOption('Access-Control-Allow-Origin', '*');
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
