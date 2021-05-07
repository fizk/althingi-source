<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\DatabaseAwareInterface;
use PDO;

class Election implements DatabaseAwareInterface
{
    use DatabaseService;

    public function get(int $id): ? Model\Election
    {
        $statement = $this->getDriver()->prepare("
            select * from `Election` where election_id = :election_id
        ");
        $statement->execute(['election_id' => $id]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\Election())->hydrate($object, new Model\Election())
            : null;
    }

    public function getByAssembly(int $assemblyId): ? Model\Election
    {
        $statement = $this->getDriver()->prepare("
            select E.* from `Election` E
            join `Election_has_Assembly` EA on (E.`election_id` = EA.`election_id`)
            where EA.`assembly_id` = :assembly_id;
        ");
        $statement->execute(['assembly_id' => $assemblyId]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\Election())->hydrate($object, new Model\Election())
            : null;
    }
}
