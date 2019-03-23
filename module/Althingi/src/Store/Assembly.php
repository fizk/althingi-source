<?php
namespace Althingi\Store;

use Althingi\Lib\StoreAwareInterface;
use Althingi\Model;
use Althingi\Hydrator;
use MongoDB\Database;

class Assembly implements StoreAwareInterface
{
    /** @var \MongoDB\Database */
    private $database;

    public function get(int $id): ?Model\AssemblyProperties
    {
        $document = $this->getStore()->assembly->findOne([
            'assembly.assembly_id' => $id
        ]);

        if (! $document) {
            return null;
        }

        $assembly = (new Hydrator\Assembly())->hydrate((array) $document->assembly, new Model\Assembly());

        $assemblyProperties = (new Model\AssemblyProperties())
            ->setAssembly($assembly);

        return $assemblyProperties;
    }

    public function setStore(Database $database)
    {
        $this->database = $database;
        return $this;
    }

    public function getStore(): Database
    {
        return $this->database;
    }
}
