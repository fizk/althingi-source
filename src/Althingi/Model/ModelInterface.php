<?php
/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 7:46 PM
 */

namespace Althingi\Model;

interface ModelInterface extends \JsonSerializable
{
    /**
     * @return array
     */
    public function toArray();
}
