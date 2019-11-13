<?php
namespace Althingi\Controller;

use Althingi\Injector\ServiceCongressmanAwareInterface;
use Althingi\Injector\ServiceIssueAwareInterface;
use Althingi\Injector\ServicePartyAwareInterface;
use Althingi\Injector\ServicePlenaryAgendaAwareInterface;
use Althingi\Injector\ServicePlenaryAwareInterface;
use Althingi\Form;
use Althingi\Model;
use Althingi\Service;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;

class PlenaryAgendaController extends AbstractRestfulController implements
    ServicePlenaryAgendaAwareInterface,
    ServicePlenaryAwareInterface,
    ServiceIssueAwareInterface,
    ServiceCongressmanAwareInterface,
    ServicePartyAwareInterface
{
    /** @var \Althingi\Service\PlenaryAgenda */
    private $plenaryAgendaService;

    /** @var \Althingi\Service\Plenary */
    private $plenaryService;

    /** @var \Althingi\Service\Issue */
    private $issueService;

    /** @var \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var \Althingi\Service\Party */
    private $partyService;

    public function getList()
    {
        $assemblyId = $this->params('id');
        $plenaryId  = $this->params('plenary_id');

        $plenary = $this->plenaryService->get($assemblyId, $plenaryId);

        $collection = array_map(function (Model\PlenaryAgenda $item) use ($plenary) {
            $returnObject = (new Model\PlenaryAgendaProperties())->setPlenaryAgenda($item);

            if ($item->getIssueId()) {
                 $returnObject->setIssue(
                     $this->issueService->get($item->getIssueId(), $item->getAssemblyId(), $item->getCategory())
                 );
            }

            if ($item->getAnswererId()) {
                $returnObject->setAnswererCongressman(
                    (new Model\CongressmanPartyProperties())
                        ->setCongressman($this->congressmanService->get($item->getAnswererId()))
                        ->setParty(
                            $this->partyService->getByCongressman($item->getAnswererId(), $plenary->getFrom())
                        )
                );
            }

            if ($item->getCounterAnswererId()) {
                $returnObject->setCounterAnswererCongressman(
                    (new Model\CongressmanPartyProperties())
                        ->setCongressman($this->congressmanService->get($item->getCounterAnswererId()))
                        ->setParty(
                            $this->partyService->getByCongressman($item->getCounterAnswererId(), $plenary->getFrom())
                        )
                );
            }

            if ($item->getInstigatorId()) {
                $returnObject->setInstigatorCongressman(
                    (new Model\CongressmanPartyProperties())
                        ->setCongressman($this->congressmanService->get($item->getInstigatorId()))
                        ->setParty(
                            $this->partyService->getByCongressman($item->getInstigatorId(), $plenary->getFrom())
                        )
                );
            }

            if ($item->getPosedId()) {
                $returnObject->setPosedCongressman(
                    (new Model\CongressmanPartyProperties())
                        ->setCongressman($this->congressmanService->get($item->getPosedId()))
                        ->setParty(
                            $this->partyService->getByCongressman($item->getPosedId(), $plenary->getFrom())
                        )
                );
            }

            return $returnObject;
        }, $this->plenaryAgendaService->fetch($assemblyId, $plenaryId));

        return new CollectionModel($collection);
    }

    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $plenaryId  = $this->params('plenary_id');
        $form = new Form\PlenaryAgenda();
        $form->bindValues(array_merge($data, [
            'item_id' => $id,
            'assembly_id' => $assemblyId,
            'plenary_id' => $plenaryId,
        ]));

        if ($form->isValid()) {
            $object = $form->getObject();
            $affectedRows = $this->plenaryAgendaService->save($object);
            return (new EmptyModel())
                ->setStatus($affectedRows === 1 ? 201 : 205);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    public function patch($id, $data)
    {
//        $assemblyId = $this->params('id');
//        $plenaryId  = $this->params('plenary_id');
//
//        if (($plenaryAgenda = $this->plenaryAgendaService->get($assemblyId, $plenaryId, $id)) != null) {
//            $form = new Form\PlenaryAgenda();
//            $form->bind($plenaryAgenda);
//            $form->setData($data);
//
//            if ($form->isValid()) {
//                $this->plenaryAgendaService->update($form->getData()); //@todo ... this doesn't exists
//                return (new EmptyModel())
//                    ->setStatus(205);
//            }
//
//            return (new ErrorModel($form))
//                ->setStatus(400);
//        }
//
//        return $this->notFoundAction();

        // Since an agenda doesn't need to be updated, but the Aggregator can call the patch endpoint
        // this method will just return an OK status
        return (new EmptyModel())
            ->setStatus(205);
    }

    /**
     * @param \Althingi\Service\PlenaryAgenda $plenaryAgenda
     * @return $this
     */
    public function setPlenaryAgendaService(Service\PlenaryAgenda $plenaryAgenda)
    {
        $this->plenaryAgendaService = $plenaryAgenda;
        return $this;
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
     * @param \Althingi\Service\Issue $issue
     * @return $this
     */
    public function setIssueService(Service\Issue $issue)
    {
        $this->issueService = $issue;
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
     * @param \Althingi\Service\Plenary $plenary
     * @return $this
     */
    public function setPlenaryService(Service\Plenary $plenary)
    {
        $this->plenaryService = $plenary;
        return $this;
    }
}
