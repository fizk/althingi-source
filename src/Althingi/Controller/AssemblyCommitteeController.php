<?php

namespace Althingi\Controller;

use Althingi\Lib\ServiceCommitteeAwareInterface;
use Althingi\Service\Committee;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;

class AssemblyCommitteeController extends AbstractRestfulController implements
    ServiceCommitteeAwareInterface
{
    /** @var  \Althingi\Service\Committee */
    private $committeeService;

    /**
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Committee
     */
    public function get($id)
    {
        $committeeId = $this->params('committee_id');
        $committee = $this->committeeService->get($committeeId);

        return $committee
            ? (new ItemModel($committee))
            : $this->notFoundAction();
    }

    /**
     * @return CollectionModel
     * @output \Althingi\Model\Committee[]
     */
    public function getList()
    {
        $assemblyId = $this->params('id');
        $committees = $this->committeeService->fetchByAssembly($assemblyId);

        return (new CollectionModel($committees))
            ->setStatus(206)
            ->setRange(0, count($committees), count($committees));
    }

    /**
     * @param Committee $committee
     */
    public function setCommitteeService(Committee $committee)
    {
        $this->committeeService = $committee;
    }
}
