<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 10/06/15
 * Time: 8:53 PM
 */

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use PDO;

/**
 * Class Party
 * @package Althingi\Service
 */
class Party implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Get one party.
     *
     * @param int $id
     * @return \stdClass
     */
    public function get($id)
    {
        $statement = $this->getDriver()->prepare('
            select * from `Party` where party_id = :party_id
        ');
        $statement->execute(['party_id' => $id]);
        return $this->decorate($statement->fetchObject());
    }

    public function getByCongressman($congressmanId, \DateTime $date)
    {
        $statement = $this->getDriver()->prepare('
            select P.* from
            (
                select * from `Session` S where
                (:date between S.`from` and S.`to`) or
                (:date >= S.`from` and S.`to` is null)
            ) S
            Join `Party` P on (P.party_id = S.party_id)
            where S.congressman_id = :congressman_id;
        ');

        $statement->execute([
            'congressman_id' => $congressmanId,
            'date' => $date->format('Y-m-d')
        ]);

        return $this->decorate($statement->fetchObject());
    }

    public function fetchTimeByAssembly($assemblyId)
    {
        $statement = $this->getDriver()->prepare('
            select sum(T.`time_sum`) as `total_time`, P.* from (
                select 
                    SP.`congressman_id`, 
                    TIME_TO_SEC(timediff(SP.`to`, SP.`from`)) as `time_sum`,
                    SE.party_id
                from `Speech` SP 
                join `Session` SE ON (SE.`congressman_id` = SP.`congressman_id` and ((SP.`from` between SE.`from` and SE.`to`) or (SP.`from` >= SE.`from` and SE.`to` is null)))
                where SP.`assembly_id` = :assembly_id
            
            ) as T
            join `Party` P on (P.`party_id` = T.`party_id`)
            group by T.`party_id`
            order by `total_time` desc;
        ');
        $statement->execute(['assembly_id' => $assemblyId]);
        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    /**
     * Get all parties that a congressman as been in.
     *
     * @param $congressmanId
     * @return array
     */
    public function fetchByCongressman($congressmanId)
    {
        $statement = $this->getDriver()->prepare(
            'select P.* from `Session` S
            join `Party` P on (P.party_id = S.party_id)
            where congressman_id = :congressman_id group by `party_id`;'
        );
        $statement->execute(['congressman_id' => $congressmanId]);
        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    /**
     * Create one Party. Accept object from corresponding
     * Form.
     *
     * @param $data
     * @return string
     */
    public function create($data)
    {
        $statement = $this->getDriver()->prepare($this->insertString('Party', $data));
        $statement->execute($this->convert($data));
        return $this->getDriver()->lastInsertId();
    }

    public function update($data)
    {
        $statement = $this->getDriver()->prepare(
            $this->updateString('Party', $data, "party_id = {$data->party_id}")
        );
        $statement->execute($this->convert($data));
        return $statement->rowCount();
    }

    private function decorate($object)
    {
        if (!$object) {
            return null;
        }

        $object->party_id = (int) $object->party_id;

        return $object;
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
}
