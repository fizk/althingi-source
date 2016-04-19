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
use Althingi\Lib\ServiceIssueAwareInterface;
use Althingi\Service\Assembly;
use Althingi\Service\Issue;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;

class AssemblyController extends AbstractRestfulController implements
    ServiceAssemblyAwareInterface,
    ServiceIssueAwareInterface
{
    use Range;

    /** @var $assemblyService \Althingi\Service\Assembly */
    private $assemblyService;

    /** @var $issueService \Althingi\Service\Issue */
    private $issueService;

    /**
     * Get one Assembly.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     */
    public function get($id)
    {
        $typeQuery = $this->params()->fromQuery('type', null);
        $types = $typeQuery ? str_split($typeQuery) : [];
        $assembly = $this->assemblyService->get($id);

        if (!$assembly) {
            return $this->notFoundAction();
        }

        $assembly->status = $this->issueService->fetchStateByAssembly($id, $types);

        return (new ItemModel($assembly))
            ->setOption('Access-Control-Allow-Origin', '*');
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
//        $assemblies = $this->assemblyService->fetchAll(
//            $range['from'],
//            ($range['to'] - $range['from']),
//            $order
//        );
        $assemblies = $this->assemblyService->fetchAll(null, null, $order);
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
                ->setStatus(201);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
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
                    ->setStatus(204);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
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
            return (new EmptyModel())->setStatus(200);
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
}
