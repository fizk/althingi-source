<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 8/06/15
 * Time: 9:16 PM
 */

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use PDO;

/**
 * Class Congressman
 * @package Althingi\Service
 */
class Congressman implements DatabaseAwareInterface
{
    const CONGRESSMAN_TYPE_MP = 'parliamentarian';
    const CONGRESSMAN_TYPE_SUBSTITUTE = 'substitute';
    const CONGRESSMAN_TYPE_WITH_SUBSTITUTE = 'with-substitute';

    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Get one Congressman.
     *
     * @param int $id
     * @return object
     */
    public function get($id)
    {
        $statement = $this->getDriver()->prepare("
            select * from `Congressman` C where congressman_id = :id
        ");
        $statement->execute(['id' => $id]);

        return $this->decorate($statement->fetchObject());
    }

    /**
     * Get all Assemblies.
     *
     * @param int $offset
     * @param int $size
     * @return array
     */
    public function fetchAll($offset, $size)
    {
        $statement = $this->getDriver()->prepare("
            select * from `Congressman` C order by C.`name` asc
            limit {$offset}, {$size}
        ");
        $statement->execute();
        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    public function fetchByAssembly($assemblyId, $congressmanType = null)
    {
        $statement;
        switch ($congressmanType) {
            case self::CONGRESSMAN_TYPE_MP:
                $statement = $this->getDriver()->prepare(
                    'select C.*, S.party_id from `Session` S
                    join `Congressman` C on (C.congressman_id = S.congressman_id)
                    where S.assembly_id = :assembly_id and S.`type` = \'þingmaður\'
                    group by S.congressman_id order by S.party_id, C.name;'
                );
                break;
            case self::CONGRESSMAN_TYPE_SUBSTITUTE:
                $statement = $this->getDriver()->prepare(
                    'select C.*, S.party_id from `Session` S
                    join `Congressman` C on (C.congressman_id = S.congressman_id)
                    where S.assembly_id = :assembly_id and S.`type` = \'varamaður\'
                    group by S.congressman_id order by S.party_id, C.name;'
                );
                break;
            case self::CONGRESSMAN_TYPE_WITH_SUBSTITUTE:
                //TODO do I need this?
                return [];
                break;
            default:
                $statement = $this->getDriver()->prepare(
                    'select C.*, S.party_id from `Session` S
                    join `Congressman` C on (C.congressman_id = S.congressman_id)
                    where S.assembly_id = :assembly_id
                    group by S.congressman_id order by S.party_id, C.name;'
                );
                break;
        }
        $statement->execute(['assembly_id' => $assemblyId]);
        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    public function fetchByCabinet($cabinetId)
    {
        $statement = $this->getDriver()->prepare(
            'select C.*, CC.`title`, CC.`from` as `date` from `Cabinet_has_Congressman` CC
            join `Congressman` C on (CC.congressman_id = C.`congressman_id`)
            where CC.`cabinet_id` = :cabinet_id order by C.`name`;'
        );
        $statement->execute(['cabinet_id' => $cabinetId]);
        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    public function fetchAccumulatedTimeByIssue($assemblyId, $issueId)
    {
        $statement = $this->getDriver()->prepare('
            select S.congressman_id, C.name, (sum(`diff`)) as `time`, date(`from`) as `begin` from (
                select *, timediff(`to`, `from`) as `diff`
                from `Speech` D
                where D.assembly_id = :assembly_id and D.issue_id = :issue_id
            ) S
            join `Congressman` C on (C.congressman_id = S.congressman_id)
            group by S.congressman_id
            order by `time` desc;
        ');
        $statement->execute([
            'issue_id' => $issueId,
            'assembly_id' => $assemblyId
        ]);

        return array_map(function ($congressman) {
            $congressman->congressman_id = (int) $congressman->congressman_id;
            $congressman->time = (int) $congressman->time;
            return $congressman;
        }, $statement->fetchAll());
    }

    public function fetchProponents($assemblyId, $documentId)
    {
        $statement = $this->getDriver()->prepare(
            'select C.* from `Document_has_Congressman` D
            join `Congressman` C on (C.congressman_id = D.congressman_id)
            where assembly_id = :assembly_id and document_id = :document_id
            order by D.`order` asc;'
        );
        $statement->execute([
            'assembly_id' => $assemblyId,
            'document_id' => $documentId
        ]);

        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    /**
     * Create one Congressman. This method accepts object
     * from corresponding Form.
     *
     * @param $data
     * @return string
     */
    public function create($data)
    {
        $statement = $this->getDriver()->prepare($this->insertString('Congressman', $data));
        $statement->execute($this->convert($data));
        return $this->getDriver()->lastInsertId();
    }

    /**
     * Update one Congressman. This method accepts object
     * from corresponding Form.
     *
     * @param $data
     * @return int Should be 1, for one entry updated.
     */
    public function update($data)
    {
        $statement = $this->getDriver()->prepare(
            $this->updateString('Congressman', $data, "congressman_id = {$data->congressman_id}")
        );
        $statement->execute($this->convert($data));
        return $statement->rowCount();
    }

    /**
     * Delete one congressman.
     *
     * @param $id
     * @return int Should be 1, for one entry deleted.
     */
    public function delete($id)
    {
        $statement = $this->getDriver()->prepare("
            select * from `Congressman`
            where congressman_id = :id
        ");
        $statement->execute(['id' => $id]);
        return $statement->rowCount();
    }

    /**
     * Count all Congressmen.
     *
     * @return int
     */
    public function count()
    {
        $statement = $this->getDriver()->prepare("
            select count(*) from `Congressman` C
        ");
        $statement->execute();
        return (int) $statement->fetchColumn(0);
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
     * @param $object
     * @return null|object
     */
    private function decorate($object)
    {
        if (!$object) {
            return null;
        }
        if (isset($object->party_id)) {
            $object->party_id = (int) $object->party_id;
        }
        $object->congressman_id = (int) $object->congressman_id;

        return $object;
    }
}
