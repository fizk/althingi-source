<?php

namespace Althingi\Controller;

use Althingi\Form\Session as SessionForm;
use Althingi\Lib\ServiceSessionAwareInterface;
use Althingi\Service\Session;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;

/**
 * Class CongressmanSessionController
 * @package Althingi\Controller
 */
class CongressmanSessionController extends AbstractRestfulController implements
    ServiceSessionAwareInterface
{
    /** @var  \Althingi\Service\Session */
    private $sessionService;

    /**
     * Get one session for Congressman.
     *
     * @param mixed $id (not used)
     * @return \Rend\View\Model\ModelInterface
     */
    public function get($id)
    {
        if ($session = $this->sessionService->get($this->params('session_id'))) {
            return new ItemModel($session);
        }

        return $this->notFoundAction();
    }

    /**
     * Patch or update one Congressman's session.
     *
     * @param $id (not used)
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function patch($id, $data)
    {
        $session = $this->sessionService->get($this->params('session_id'));

        if (!$session) {
            return $this->notFoundAction();
        }

        $form = (new SessionForm())
            ->bind($session)
            ->setData($data);

        if ($form->isValid()) {
            $this->sessionService->update($form->getData());
            return (new EmptyModel())->setStatus(205);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * Delete one Congressman's session
     *
     * @param int $id (not used)
     * @return \Rend\View\Model\ModelInterface
     */
    public function delete($id)
    {
        $this->sessionService->delete($this->params('session_id'));

        return (new EmptyModel())->setStatus(204);
    }

    /**
     * @param Session $session
     */
    public function setSessionService(Session $session)
    {
        $this->sessionService = $session;
    }
}
