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
use Zend\Form\FormInterface;

/**
 * Class CongressmanSessionController
 * @package Althingi\Controller
 */
class CongressmanSessionController extends AbstractRestfulController
{
    /**
     * Get one session for Congressman.
     *
     * @param mixed $id (not used)
     * @return ItemModel
     */
    public function get($id)
    {
        $sessionService = $this->getServiceLocator()
            ->get('Althingi\Service\Session');

        if ($session = $sessionService->get($this->params('session_id'))) {
            return new ItemModel($session);
        }

        return $this->notFoundAction();
    }

    /**
     * Patch or update one Congressman's session.
     *
     * @param $id (not used)
     * @param $data
     * @return EmptyModel|ErrorModel
     */
    public function patch($id, $data)
    {
        $sessionService = $this->getServiceLocator()
            ->get('Althingi\Service\Session');
        $session = $sessionService->get($this->params('session_id'));

        if (!$session) {
            return $this->notFoundAction();
        }

        $form = (new Session())
            ->bind($session)
            ->setData($data);

        if ($form->isValid()) {
            $sessionService->update($form->getData());
            return (new EmptyModel())->setStatus(204);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * Delete one Congressman's session
     *
     * @param int $id (not used)
     * @return EmptyModel
     */
    public function delete($id)
    {
        $sessionService = $this->getServiceLocator()
            ->get('Althingi\Service\Session');
        $sessionService->delete($this->params('session_id'));

        return (new EmptyModel())->setStatus(204);
    }
}
