<?php
namespace Althingi\Presenters;

interface IndexablePresenterAwareInterface
{
    /**
     * @return IndexablePresenter
     */
    public function getPresenter(): IndexablePresenter;

    /**
     * @param IndexablePresenter $presenter
     * @return IndexablePresenterAwareInterface
     */
    public function setPresenter(IndexablePresenter $presenter): IndexablePresenterAwareInterface;
}
