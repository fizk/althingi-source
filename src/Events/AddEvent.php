<?php

namespace Althingi\Events;

use Althingi\Presenters\IndexablePresenter;
use Althingi\Presenters\IndexablePresenterAwareInterface;
use Laminas\EventManager\Event;

class AddEvent extends Event implements IndexablePresenterAwareInterface
{
    /** @var  \Althingi\Presenters\IndexablePresenter */
    private $presenter;

    public function __construct(IndexablePresenter $presenter, $params = null)
    {
        parent::__construct('add', $this, $params);
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
