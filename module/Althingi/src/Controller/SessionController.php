<?php

namespace Althingi\Controller;

use Althingi\Form\Session as SessionForm;
use Althingi\Lib\ServiceSessionAwareInterface;
use Althingi\Service\Session;
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
class SessionController extends AbstractRestfulController implements
    ServiceSessionAwareInterface
{
    /** @var  \Althingi\Service\Session */
    private $sessionService;

    /**
     * Get sessions by one Congressman.
     * This is therefor a list.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Session
     */
    public function get($id)
    {
        if (($session = $this->sessionService->get($id)) != null) {
            return new ItemModel($session);
        }

        return $this->notFoundAction();
    }

    /**
     * Get all sessions my congressman.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Session[]
     */
    public function getList()
    {
        $congressmanId = $this->params('congressman_id');

        $sessions = $this->sessionService->fetchByCongressman($congressmanId);
        $sessionsCount = count($sessions);

        return (new CollectionModel($sessions))
            ->setStatus(206)
            ->setRange(0, $sessionsCount, $sessionsCount);
    }

    /**
     * Create a new Congressman session.
     *
     * @todo Session do not have IDs coming from althingi.is.
     *  They are created on this server. To be able to update
     *  these entries, the server has to provide the client with
     *  the URI created on the server. This method will try to
     *  create a resource and if the DB responds with a 23000 (ER_DUP_KEY)
     *  it will then try to find the server's ID and create an URI
     *  from that and pass it back in the HTTP's header Location as
     *  well as issuing a 409 response code. The client can then
     *  try to do a PATCH request with the URI provided.
     *
     *  If althingi.is will start to provide a session_id, then this will
     *  not be needed as the resource wil be stores via PUSH request.
     *
     *  To facilitate that, create a self::push() method and remove
     *  \Althingi\Service\Session::getIdentifier()
     *
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Session
     */
    public function post($data)
    {
        $congressmanId = $this->params('congressman_id');
        $statusCode = 201;
        $sessionId = 0;

        $form = new SessionForm();
        $form->setData(array_merge($data, ['congressman_id' => $congressmanId]));

        if ($form->isValid()) {
            /** @var $sessionObject \Althingi\Model\Session */
            $sessionObject = $form->getObject();

            try {
                $sessionId = $this->sessionService->create($sessionObject);
                $statusCode = 201;
            } catch (\Exception $e) {
                // Error: 1022 SQLSTATE: 23000 (ER_DUP_KEY)
                if ($e->getCode() == 23000) {
                    $sessionId = $this->sessionService->getIdentifier(
                        $sessionObject->getCongressmanId(),
                        $sessionObject->getFrom(),
                        $sessionObject->getType()
                    );
                    $statusCode = 409;
                }
            }

            return (new EmptyModel())
                ->setLocation(
                    $this->url()->fromRoute(
                        'thingmenn/thingseta',
                        ['congressman_id' => $congressmanId, 'session_id' => $sessionId]
                    )
                )
                ->setStatus($statusCode);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Session
     */
    public function patch($id, $data)
    {
        if (($session = $this->sessionService->get($id)) != null) {
            $form = new SessionForm();
            $form->bind($session);
            $form->setData($data);

            if ($form->isValid()) {
                $this->sessionService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }

    /**
     * @param Session $session
     * @return $this
     */
    public function setSessionService(Session $session)
    {
        $this->sessionService = $session;
        return $this;
    }
}
