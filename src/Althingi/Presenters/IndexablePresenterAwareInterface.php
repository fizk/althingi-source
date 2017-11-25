<?php
/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 10/21/17
 * Time: 4:23 PM
 */

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
