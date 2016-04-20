<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 8/06/15
 * Time: 9:05 PM
 */

namespace Althingi\Controller;

use Althingi\Form\Congressman as CongressmanForm;
use Althingi\Lib\ServiceCongressmanAwareInterface;
use Althingi\Service\Congressman;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;

class CongressmanController extends AbstractRestfulController implements
    ServiceCongressmanAwareInterface
{
    use Range;

    /** @var \Althingi\Service\Congressman */
    private $congressmanService;

    /**
     * Return list of congressmen.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function getList()
    {
        $count = $this->congressmanService->count();
        $range = $this->getRange($this->getRequest(), $count);
        $assemblies = $this->congressmanService->fetchAll($range['from'], $range['to']);

        return (new CollectionModel($assemblies))
            ->setStatus(206)
            ->setRange($range['from'], $range['to'], $count);
    }

    /**
     * Get one congressman.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     */
    public function get($id)
    {
        if ($congressman = $this->congressmanService->get($id)) {
            return (new ItemModel($congressman))
                ->setOption('Access-Control-Allow-Origin', '*');
        }

        return $this->notFoundAction();
    }

    /**
     * Create on congressman entry.
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function put($id, $data)
    {
        $form = new CongressmanForm();
        $form->setData(array_merge($data, ['congressman_id' => $id]));

        if ($form->isValid()) {
            $this->congressmanService->create($form->getObject());
            return (new EmptyModel())->setStatus(201);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * Update congressman.
     *
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function patch($id, $data)
    {
        $congressman = $this->congressmanService->get($id);

        if (!$congressman) {
            return $this->notFoundAction();
        }

        $form = (new CongressmanForm())
            ->bind($congressman)
            ->setData($data);

        if ($form->isValid()) {
            $this->congressmanService->update($form->getObject());
            return (new EmptyModel())->setStatus(204);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * Create new Congressman allowing the system
     * to auto-generate the ID.
     *
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function create($data)
    {
        $form = (new CongressmanForm())
            ->setData($data);

        if ($form->isValid()) {
            $id = $this->congressmanService->create($form->getObject());
            return (new EmptyModel())
                ->setLocation($this->url()->fromRoute('thingmenn', ['id' => $id]))
                ->setStatus(201);
        }
        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     */
    public function delete($id)
    {
        $this->congressmanService->delete($id);

        return (new EmptyModel())->setStatus(204);
    }

    /**
     * @param Congressman $congressman
     */
    public function setCongressmanService(Congressman $congressman)
    {
        $this->congressmanService = $congressman;
    }
}
