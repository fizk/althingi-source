<?php

namespace Althingi\Controller;

use Althingi\Injector\ServiceCommitteeAwareInterface;
use Althingi\Service\Committee;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
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
     * @200 Success
     * @404 Resource not found
     */
    public function get($id)
    {
        $committeeId = $this->params('committee_id');
        $committee = $this->committeeService->get($committeeId);

        return $committee
            ? (new ItemModel($committee))->setStatus(200)
            : (new ErrorModel('Resource Not Found'))->setStatus(404);
    }

    /**
     * @return CollectionModel
     * @output \Althingi\Model\Committee[]
     * @206 Success
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
     * @return $this
     */
    public function setCommitteeService(Committee $committee)
    {
        $this->committeeService = $committee;
        return $this;
    }
}
