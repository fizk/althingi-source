<?php
namespace Althingi\Command;

use Althingi\Model\ModelInterface;

interface CommandInterface
{
    public function exec(): ModelInterface;
}
