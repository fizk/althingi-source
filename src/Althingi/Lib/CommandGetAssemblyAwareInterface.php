<?php
/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/20/16
 * Time: 4:13 PM
 */

namespace Althingi\Lib;

use Althingi\Command\GetAssembly;

interface CommandGetAssemblyAwareInterface
{
    public function setGetAssemblyCommand(GetAssembly $command);
}