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
use Althingi\Lib\ServiceCategoryAwareInterface;
use Althingi\Lib\ServiceElectionAwareInterface;
use Althingi\Lib\ServiceIssueAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Lib\ServiceSpeechAwareInterface;
use Althingi\Lib\ServiceVoteAwareInterface;
use Althingi\Service\Assembly;
use Althingi\Service\Cabinet;
use Althingi\Service\Category;
use Althingi\Service\Election;
use Althingi\Service\Issue;
use Althingi\Service\Party;
use Althingi\Service\Speech;
use Althingi\Service\Vote;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;

class AssemblyController extends AbstractRestfulController implements
    ServiceAssemblyAwareInterface,
    ServiceIssueAwareInterface,
    ServicePartyAwareInterface,
    ServiceVoteAwareInterface,
    ServiceSpeechAwareInterface,
    ServiceCabinetAwareInterface,
    ServiceCategoryAwareInterface,
    ServiceElectionAwareInterface
{
    use Range;

    /** @var $assemblyService \Althingi\Service\Assembly */
    private $assemblyService;

    /** @var $issueService \Althingi\Service\Issue */
    private $issueService;

    /** @var $issueService \Althingi\Service\Vote */
    private $voteService;

    /** @var $issueService \Althingi\Service\Speech */
    private $speechService;

    /** @var $issueService \Althingi\Service\Party */
    private $partyService;

    /** @var $issueService \Althingi\Service\Cabinet */
    private $cabinetService;

    /** @var $issueService \Althingi\Service\Category */
    private $categoryService;

    /** @var $issueService \Althingi\Service\Election */
    private $electionService;

    /**
     * Get one Assembly.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     */
    public function get($id)
    {
        if (($assembly = $this->assemblyService->get($id)) != null) {
            $assembly->parties = [];
            $cabinets = $this->cabinetService->fetchByAssembly($id);
            foreach ($cabinets as $cabinet) {
                $majority = $this->partyService->fetchByCabinet($cabinet->cabinet_id);
                $assembly->parties[] = [
                    'majority' => $majority,
                    'minority' => $this->partyService->fetchByAssembly($id, array_map(function ($party) {
                        return (int) $party->party_id;
                    }, $majority)),
                ];
            }

            if (count($assembly->parties) > 1 && $assembly->parties[0] == $assembly->parties[1]) {
                $assembly->parties = [$assembly->parties[0]];
            }

            return (new ItemModel($assembly))
                ->setStatus(200)
                ->setOption('Access-Control-Allow-Origin', '*');
        }

        return $this->notFoundAction();
    }

    /**
     * Return list of Assemblies.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function getList()
    {
        $order = $this->params()->fromQuery('order', 'desc');

        $count = $this->assemblyService->count();
        $range = $this->getRange($this->getRequest(), $count);
        $assemblies = $this->assemblyService->fetchAll(
            $range['from'],
            ($range['to'] - $range['from']),
            $order
        );

        foreach ($assemblies as $assembly) {
            $assembly->parties = [];
            $cabinets = $this->cabinetService->fetchByAssembly($assembly->assembly_id);
            foreach ($cabinets as $cabinet) {
                $assembly->parties[] = [
                    'majority' => $this->partyService->fetchByCabinet($cabinet->cabinet_id)
                ];
            }
        }

        return (new CollectionModel($assemblies))
            ->setOption('Access-Control-Allow-Origin', '*')
            ->setOption('Access-Control-Expose-Headers', 'Range-Unit, Content-Range') //TODO should go into Rend
            ->setStatus(206)
            ->setRange(0, $count, $count);
    }

    /**
     * Create new Resource Assembly.
     *
     * @param  int $id
     * @param  array $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function put($id, $data)
    {
        $form = new AssemblyForm();
        $form->bindValues(array_merge($data, ['assembly_id' => $id]));

        if ($form->isValid()) {
            $object = $form->getObject();
            $this->assemblyService->create($object);
            return (new EmptyModel())
                ->setStatus(201)
                ->setOption('Access-Control-Allow-Origin', '*');
        }

        return (new ErrorModel($form))
            ->setStatus(400)
            ->setOption('Access-Control-Allow-Origin', '*');
    }

    /**
     * List options for Assembly collection.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function optionsList()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['GET', 'OPTIONS'])
            ->setOption('Access-Control-Allow-Origin', '*');
    }

    /**
     * List options for Assembly entry.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function options()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['GET', 'OPTIONS', 'PUT', 'PATCH', 'DELETE'])
            ->setOption('Access-Control-Allow-Origin', '*');
    }

    /**
     * Get statistics about assembly.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function statisticsAction()
    {
        $assemblyId = $this->params('id');

        $response = (object)[
            'bills' => $this->issueService->fetchNonGovernmentBillStatisticsByAssembly($assemblyId),
            'government_bills' => $this->issueService->fetchGovernmentBillStatisticsByAssembly($assemblyId),
            'types' => $this->issueService->fetchStateByAssembly($assemblyId),
            'votes' => $this->voteService->fetchFrequencyByAssembly($assemblyId),
            'speeches' => $this->speechService->fetchFrequencyByAssembly($assemblyId),
            'party_times' => $this->partyService->fetchTimeByAssembly($assemblyId),
            'categories' => $this->categoryService->fetchByAssembly($assemblyId),
            'election' => $this->electionService->getByAssembly($assemblyId),
            'election_results' => $this->partyService->fetchElectedByAssembly($assemblyId)
        ];

        return (new ItemModel($response))
            ->setStatus(200)
            ->setOption('Access-Control-Allow-Origin', '*');
    }

    /**
     * Update one Assembly
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function patch($id, $data)
    {
        if (($assembly = $this->assemblyService->get($id)) != null) {
            $form = new AssemblyForm();
            $form->bind($assembly);
            $form->setData($data);

            if ($form->isValid()) {
                $this->assemblyService->update($form->getData());
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
     * Delete one Assembly.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     */
    public function delete($id)
    {
        if (($assembly = $this->assemblyService->get($id)) != null) {
            $this->assemblyService->delete($id);
            return (new EmptyModel())
                ->setStatus(200)
                ->setOption('Access-Control-Allow-Origin', '*');
        }

        return $this->notFoundAction();
    }

    /**
     * @param Assembly $assembly
     */
    public function setAssemblyService(Assembly $assembly)
    {
        $this->assemblyService = $assembly;
    }

    /**
     * @param Issue $issue
     */
    public function setIssueService(Issue $issue)
    {
        $this->issueService = $issue;
    }

    /**
     * @param Party $party
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
    }

    /**
     * @param Speech $speech
     */
    public function setSpeechService(Speech $speech)
    {
        $this->speechService = $speech;
    }

    /**
     * @param Vote $vote
     */
    public function setVoteService(Vote $vote)
    {
        $this->voteService = $vote;
    }

    /**
     * @param Cabinet $cabinet
     */
    public function setCabinetService(Cabinet $cabinet)
    {
        $this->cabinetService = $cabinet;
    }

    /**
     * @param Category $category
     */
    public function setCategoryService(Category $category)
    {
        $this->categoryService = $category;
    }

    /**
     * @param Election $election
     */
    public function setElectionService(Election $election)
    {
        $this->electionService = $election;
    }
}
