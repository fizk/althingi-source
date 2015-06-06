<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 26/04/15
 * Time: 9:08 PM
 */

namespace Althingi\View\Model;

/**
 * Interface ModelInterface
 * @package Restvisi\View\Model
 */
interface ModelInterface extends \Serializable
{
    /**
     * Set HTTP location header
     * @param $location
     * @return ModelInterface
     */
    public function setLocation($location);

    /**
     * Set HTTP length header.
     *
     * @param int $length
     * @return ModelInterface
     */
    public function setLength($length);

    /**
     * Set HTTP status code.
     *
     * @param $code
     * @return ModelInterface
     */
    public function setStatus($code);

    /**
     * Get HTTP status code.
     *
     * @return int
     */
    public function getStatus();
}
