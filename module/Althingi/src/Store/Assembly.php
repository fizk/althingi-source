<?php
namespace Althingi\Store;

use Althingi\Injector\StoreAwareInterface;
use Althingi\Model;
use Althingi\Hydrator;
use MongoDB\Database;

class Assembly implements StoreAwareInterface
{
    /** @var \MongoDB\Database */
    private $database;

    /**
     * @param int $id
     * @return Model\AssemblyProperties|null
     */
    public function get(int $id): ?Model\AssemblyProperties
    {
        $document = $this->getStore()->selectCollection('assembly')->findOne([
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


    public function fetch(): array
    {
        $document = $this->getStore()->selectCollection('assembly')->find(
            [],
            ['sort' => ['assembly.assembly_id' => -1]]
        );

        if (! $document) {
            return [];
        }

        return array_map(function ($assembly) {
            $assembly = (new Hydrator\Assembly())->hydrate((array) $assembly->assembly, new Model\Assembly());
            return (new Model\AssemblyProperties())
                ->setAssembly($assembly);
        }, $document->toArray());
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
