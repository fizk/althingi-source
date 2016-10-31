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
class CommitteeMeetingAgenda implements DatabaseAwareInterface
{
    use DatabaseService;

    /** @var  \PDO */
    private $pdo;

    public function get($meetingId, $agendaId)
    {
        $statement = $this->getDriver()->prepare('
            select * from `CommitteeMeetingAgenda` C 
            where C.`committee_meeting_id` = :committee_meeting_id 
              and C.`committee_meeting_agenda_id` = :committee_meeting_agenda_id;
        ');
        $statement->execute([
            'committee_meeting_id' => $meetingId,
            'committee_meeting_agenda_id' => $agendaId
        ]);

        return $this->decorate($statement->fetchObject());
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
            ->prepare($this->insertString('CommitteeMeetingAgenda', $data));
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
                'CommitteeMeetingAgenda',
                $data,
                "committee_meeting_id={$data->committee_meeting_id} " .
                "and committee_meeting_agenda_id={$data->committee_meeting_agenda_id}"
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
        $object->issue_id = $object->issue_id ? (int) $object->assembly_id : null;
        $object->committee_meeting_id = (int) $object->committee_meeting_id;
        $object->committee_meeting_agenda_id = (int) $object->committee_meeting_agenda_id;

        return $object;
    }
}
