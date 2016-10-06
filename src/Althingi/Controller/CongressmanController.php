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
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Lib\ServiceSessionAwareInterface;
use Althingi\Service\Congressman;
use Althingi\Service\Party;
use Althingi\Service\Session;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;

class CongressmanController extends AbstractRestfulController implements
    ServiceCongressmanAwareInterface,
    ServicePartyAwareInterface,
    ServiceSessionAwareInterface
{
    use Range;

    /** @var \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var \Althingi\Service\Party */
    private $partyService;

    /** @var \Althingi\Service\Session */
    private $sessionService;

    /**
     * Get one congressman.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     */
    public function get($id)
    {
        if ($congressman = $this->congressmanService->get($id)) {
            $congressman->parties = $this->partyService->fetchByCongressman($id);

            return (new ItemModel($congressman))
                ->setStatus(200)
                ->setOption('Access-Control-Allow-Origin', '*');
        }

        return $this->notFoundAction();
    }

    /**
     * Return list of congressmen.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function getList()
    {
        $count = $this->congressmanService->count();
        $range = $this->getRange($this->getRequest(), $count);
        $congressmen = $this->congressmanService->fetchAll($range['from'], $range['to']);

        return (new CollectionModel($congressmen))
            ->setStatus(206)
            ->setRange($range['from'], $range['to'], $count)
            ->setOption('Access-Control-Expose-Headers', 'Range, Range-Unit, Content-Range') //TODO should go into Rend
            ->setOption('Access-Control-Allow-Origin', '*');
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
            return (new EmptyModel())
                ->setStatus(201)
                ->setOption('Access-Control-Allow-Origin', '*');
        }

        return (new ErrorModel($form))
            ->setStatus(400)
            ->setOption('Access-Control-Allow-Origin', '*');
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
        if (($congressman = $this->congressmanService->get($id)) != null) {
            $form = (new CongressmanForm())
                ->bind($congressman)
                ->setData($data);

            if ($form->isValid()) {
                $this->congressmanService->update($form->getObject());
                return (new EmptyModel())
                    ->setStatus(204)
                    ->setOption('Access-Control-Allow-Origin', '*');
            }

            return (new ErrorModel($form))
                ->setStatus(400)
                ->setOption('Access-Control-Allow-Origin', '*');
        }

        return $this->notFoundAction();
    }

    /**
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     */
    public function delete($id)
    {
        if (($congressman = $this->congressmanService->get($id))) {
            $this->congressmanService->delete($id);

            return (new EmptyModel())
                ->setStatus(204)
                ->setOption('Access-Control-Allow-Origin', '*');
        }

        return $this->notFoundAction();
    }

    /**
     * Get all members of assembly.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function assemblyAction()
    {
        $assemblyId = $this->params('id');

        $congressmen = array_map(function ($congressman) {
            $congressman->party = $this->partyService->get($congressman->party_id);
            return $congressman;
        }, $this->congressmanService->fetchByAssembly($assemblyId));

        return (new CollectionModel($congressmen))
            ->setStatus(200)
            ->setOption('Access-Control-Allow-Origin', '*');

    }

    public function optionsList()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['GET', 'OPTIONS'])
            ->setOption('Access-Control-Allow-Origin', '*')
            ->setOption('Access-Control-Expose-Headers', 'Range, Range-Unit, Content-Range')
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
            ->setOption('Access-Control-Allow-Origin', '*')
            ->setOption('Access-Control-Expose-Headers', 'Range, Range-Unit, Content-Range')
    }
    
    /**
     * @param Congressman $congressman
     */
    public function setCongressmanService(Congressman $congressman)
    {
        $this->congressmanService = $congressman;
    }

    /**
     * @param Party $party
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
    }

    /**
     * @param Session $session
     */
    public function setSessionService(Session $session)
    {
        $this->sessionService = $session;
    }
}
