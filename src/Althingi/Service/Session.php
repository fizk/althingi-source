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
use DateTime;

/**
 * Class Session
 * @package Althingi\Service
 */
class Session implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Get one Congressman's Session.
     *
     * @param $id
     * @return null|object
     */
    public function get($id)
    {
        $statement = $this->getDriver()->prepare("
            select * from `Session` where session_id = :session_id
        ");
        $statement->execute(['session_id' => $id]);
        return $this->decorate($statement->fetchObject());
    }

    /**
     * Fetch all Session by Congressman.
     *
     * @param $id
     * @return array
     */
    public function fetchByCongressman($id)
    {
        $statement =$this->getDriver()->prepare("
            select * from `Session` where congressman_id = :id
            order by `from` desc
        ");
        $statement->execute(['id' => $id]);
        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    public function fetchByAssemblyAndCongressman($assemblyId, $congressmanId)
    {
        $statement = $this->getDriver()->prepare("
            select * from `Session` S where S.`congressman_id` = :congressman_id and S.`assembly_id` = :assembly_id 
            order by `from` desc
        ");

        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
        ]);
        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    public function getIdentifier($congressmanId, DateTime $from, $type)
    {
        $statement = $this->getDriver()->prepare('
            select `session_id` from `Session`
            where `congressman_id` = :congressman_id and `type` = :type and `from` = :from;
        ');
        $statement->execute([
            'congressman_id' => $congressmanId,
            'type' => $type,
            'from' => $from->format('Y-m-d'),
        ]);
        return $statement->fetchColumn(0);
    }

    /**
     * Create one entry. Accepts object from
     * corresponding Form.
     *
     * @param object $data
     * @return int affected rows
     */
    public function create($data)
    {
        $statement = $this
            ->getDriver()
            ->prepare($this->insertString('Session', $data));
        $statement->execute($this->convert($data));
        return $this->getDriver()->lastInsertId();
    }

    /**
     * Update one Congressman's Session. Accepts object from
     * corresponding Form.
     *
     * @param $data
     * @return string
     */
    public function update($data)
    {
        $statement = $this
            ->getDriver()
            ->prepare($this->updateString('Session', $data, "session_id = {$data->session_id}"));
        $statement->execute($this->convert($data));
        return $this->getDriver()->lastInsertId();
    }

    /**
     * Delete one Congressman's session.
     *
     * @param $id
     * @return int
     */
    public function delete($id)
    {
        $statement = $this->getDriver()->prepare("
            delete from `Session` where session_id = :id
        ");
        $statement->execute(['id' => $id]);
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

    /**
     * Decorate and convert one entry object.
     *
     * Adds Congressman object, Constituency object
     * and Party object to Speech.
     *
     * @param $object
     * @return null|object
     */
    private function decorate($object)
    {
        if (!$object) {
            return null;
        }

        $constituencyStatement = $this->getDriver()->prepare("
            select `constituency_id` as id, `name`, `abbr_short` as abbr
            from `Constituency` where constituency_id = :id
        ");
        $constituencyStatement->execute(['id' => $object->constituency_id]);

        $partyStatement = $this->getDriver()->prepare("
            select `party_id` as id, `name`, `abbr_short` as abbr
            from `Party` where party_id = :id
        ");
        $partyStatement->execute(['id' => $object->party_id]);


        $object->session_id = (int) $object->session_id;
        $object->congressman_id = (int) $object->congressman_id;
        $object->constituency = $constituencyStatement->fetchObject();
        $object->party = $partyStatement->fetchObject();

        unset($object->constituency_id);
        unset($object->party_id);
        return $object;
    }
}
