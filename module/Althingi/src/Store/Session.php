<?php
namespace Althingi\Store;

use Althingi\Injector\StoreAwareInterface;
use Althingi\Model;
use Althingi\Hydrator;
use MongoDB\Database;

class Session implements StoreAwareInterface
{
    /** @var \MongoDB\Database */
    private $database;

    /**
     * @param $assemblyId
     * @param $congressmanId
     * @return \Althingi\Model\Session[]
     */
    public function fetchByAssemblyAndCongressman($assemblyId, $congressmanId): array
    {
        $document = $this->getStore()->congressman->aggregate([
            ['$match' => [
                'assembly.assembly_id' => (int)$assemblyId,
                'congressman.congressman_id' => (int)$congressmanId
            ]],
            ['$project' => [
                'sessions' => 1
            ]],
        ]);

        $doc = $document->toArray();

        if (count($doc) != 1) {
            return [];
        }

        return array_map(function ($session) {
            return (new Hydrator\Session())->hydrate((array)$session, new Model\Session());
        }, (array)$doc[0]['sessions']);
    }

    /**
     * @param int $id
     * @return Model\AssemblyProperties|null
     */
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
