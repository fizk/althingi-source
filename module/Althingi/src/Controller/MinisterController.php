<?php

namespace Althingi\Controller;

use Althingi\Injector\ServiceMinistryAwareInterface;
use Althingi\Service\Ministry;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;

/**
 * Class MinisterController
 * @package Althingi\Controller
 */
class MinisterController extends AbstractRestfulController implements ServiceMinistryAwareInterface
{

    /** @var  \Althingi\Service\Ministry */
    private $ministryService;

    /**
     * Get ministry by one Congressman by given assembly.
     * This is therefor a list.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Ministry
     * @200 Success
     * @404 Resource not found
     */
    public function get($id)
    {
        $assemblyId = $this->params('id');
        $congressmanId = $this->params('congressman_id');

        $ministry = $this->ministryService->getByCongressmanAssembly(
            $assemblyId,
            $congressmanId,
            $id
        );

        return $ministry
            ? (new ItemModel($ministry))->setStatus(200)
            : (new ErrorModel('Resource Not Found'))->setStatus(404);
    }

    /**
     * * Get all ministries by one Congressman by given assembly.
     *
     * @return CollectionModel|\Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Ministry[]
     * @206 Success
     */
    public function getList()
    {
        $assemblyId = $this->params('id');
        $congressmanId = $this->params('congressman_id');

        $ministries = $this->ministryService->fetchByCongressmanAssembly($assemblyId, $congressmanId);

        return (new CollectionModel($ministries))
            ->setStatus(206)
            ->setRange(0, count($ministries), count($ministries));
    }

    /**
     * @param \Althingi\Service\Ministry $ministry
     * @return $this
     */
    public function setMinistryService(Ministry $ministry)
    {
        $this->ministryService = $ministry;
        return $this;
    }
}
