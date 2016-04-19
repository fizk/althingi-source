<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/03/2016
 * Time: 11:22 AM
 */

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use PDO;

class VoteItem implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    public function fetchByVote($id)
    {
        $statement = $this->getDriver()->prepare(
            'select * from `VoteItem` where vote_id = :vote_id'
        );
        $statement->execute(['vote_id' => $id]);
        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    public function create($data)
    {
        $insertStatement = $this->getDriver()->prepare($this->insertString('VoteItem', $data));
        $insertStatement->execute($this->convert($data));
        return (int) $this->getDriver()->lastInsertId();
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

        $object->vote_id = (int) $object->vote_id;
        $object->congressman_id = (int) $object->congressman_id;
        return $object;
    }
}
