<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 8/06/15
 * Time: 9:05 PM
 */

namespace Althingi\Controller;

use Althingi\Form\Session;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;

/**
 * Class SessionController
 * @package Althingi\Controller
 * @todo PUT / PATCH
 */
class SessionController extends AbstractRestfulController
{
    /**
     * Get sessions by one Congressman.
     * This is therefor a list.
     *
     * @param int $id
     * @return \Althingi\View\Model\ItemModel
     * @todo should there be a test if the congressman exists?
     */
    public function get($id)
    {
        $sessionService = $this->getServiceLocator()
            ->get('Althingi\Service\Session');
        $sessions = $sessionService->fetchByCongressman($id);

        return new CollectionModel($sessions);
    }

    /**
     * Create a new Congressman session.
     *
     * @param mixed $data
     * @return ItemModel
     */
    public function post($data)
    {
        $sessionService = $this->getServiceLocator()
            ->get('Althingi\Service\Session');

        $congressmanId = $this->params('congressman_id');

        $form = new Session();
        $form->setData(array_merge($data, ['congressman_id' => $congressmanId]));

        if ($form->isValid()) {
            $sessionId = $sessionService->create($form->getObject());
            return (new EmptyModel())
                ->setLocation(
                    $this->url()->fromRoute(
                        'home/thingmenn/thingseta',
                        ['congressman_id' => $congressmanId, 'session_id' => $sessionId]
                    )
                )
                ->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
    }
}
