<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 19/05/15
 * Time: 1:02 PM
 */

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use PDO;

/**
 * Class Assembly
 * @package Althingi\Service
 */
class CommitteeMeeting implements DatabaseAwareInterface
{
    use DatabaseService;

    /** @var  \PDO */
    private $pdo;

    public function get($id)
    {
        $statement = $this->getDriver()->prepare('
            select * from `CommitteeMeeting` where committee_meeting_id = :committee_meeting_id
        ');
        $statement->execute([
            'committee_meeting_id' => $id,
        ]);
        return $this->decorate($statement->fetchObject());
    }

    public function fetchByAssembly($assemblyId, $committeeId)
    {
        $statement = $this->getDriver()->prepare('
            select * from `CommitteeMeeting` C where assembly_id = :assembly_id and committee_id = :committee_id
            order by C.`from`
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'committee_id' => $committeeId,
        ]);

        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    /**
     * Create one entry.
     *
     * @param object $data
     * @return int affected rows
     */
    public function create($data)
    {
        $statement = $this
            ->getDriver()
            ->prepare($this->insertString('CommitteeMeeting', $data));
        $statement->execute($this->convert($data));
        return $statement->rowCount();
    }

    /**
     * Create one entry.
     *
     * @param object $data
     * @return int affected rows
     */
    public function update($data)
    {
        $statement = $this
            ->getDriver()
            ->prepare($this->updateString(
                'CommitteeMeeting',
                $data,
                "committee_meeting_id={$data->committee_meeting_id}"
            ));
        $statement->execute($this->convert($data));
        return $statement->rowCount();
    }

    /**
     * @param \PDO $pdo
     */
    public function setDriver(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return \PDO
     */
    public function getDriver()
    {
        return $this->pdo;
    }

    private function decorate($object)
    {
        if (!$object) {
            return null;
        }

        $object->assembly_id = (int) $object->assembly_id;
        $object->committee_id = (int) $object->committee_id;
        $object->committee_meeting_id = (int) $object->committee_meeting_id;

        return $object;
    }
}
