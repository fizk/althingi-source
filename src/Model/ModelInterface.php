<?php
namespace Althingi\Model;

interface ModelInterface extends \JsonSerializable
{
    /**
     * @return array
     */
    public function toArray(): array;
}
