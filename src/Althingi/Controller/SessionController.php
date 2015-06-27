<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 8/06/15
 * Time: 9:05 PM
 */

namespace Althingi\Controller;

use Althingi\Form\Session;
use Althingi\View\Model\CollectionModel;
use Althingi\View\Model\EmptyModel;
use Althingi\View\Model\ErrorModel;
use Althingi\View\Model\ItemModel;

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
    public function create($data)
    {
        $sessionService = $this->getServiceLocator()
            ->get('Althingi\Service\Session');

        $congressmanId = $this->params('id');


        $form = new Session();
        $form->setData(array_merge($data, ['congressman_id' => $congressmanId]));

        if ($form->isValid()) {
            $sessionId = $sessionService->create($form->getObject());
            return (new EmptyModel())
                ->setLocation(
                    $this->url()->fromRoute(
                        'home/thingseta/fundur',
                        ['id' => $congressmanId, 'session_id' => $sessionId]
                    )
                )
                ->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
    }
}
