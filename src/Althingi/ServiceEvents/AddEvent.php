<?php

namespace Althingi\ServiceEvents;

use Althingi\Model\ModelIdentityInterface;
use Althingi\Presenters\IndexablePresenter;
use Althingi\Presenters\IndexablePresenterAwareInterface;
use Zend\EventManager\Event;

class AddEvent extends Event implements IndexablePresenterAwareInterface
{
    /** @var  \Althingi\Presenters\IndexablePresenter */
    private $presenter;

    public function __construct(IndexablePresenter $presenter)
    {
        parent::__construct('add');
        $this->setPresenter($presenter);
    }

    /**
     * @return IndexablePresenter
     */
    public function getPresenter(): IndexablePresenter
    {
        return $this->presenter;
    }

    /**
     * @param IndexablePresenter $presenter
     * @return IndexablePresenterAwareInterface
     */
    public function setPresenter(IndexablePresenter $presenter): IndexablePresenterAwareInterface
    {
        $this->presenter = $presenter;
        return $this;
    }
}
