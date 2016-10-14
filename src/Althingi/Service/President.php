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
 * Class President
 * @package Althingi\Service
 */
class President implements DatabaseAwareInterface
{
    use DatabaseService;


    /**
     * @var \PDO
     */
    private $pdo;

    public function get($id)
    {
        $statement = $this->getDriver()->prepare("
            select * from `President` P where P.`president_id` = :president_id;
        ");
        $statement->execute(['president_id' => $id]);

        return $this->decorate($statement->fetchObject());
    }

    public function getByUnique($assemblyId, $congressmanId, \DateTime $from, $title)
    {
        $statement = $this->getDriver()->prepare("
            select * from `President` P 
            where P.`assembly_id` = :assembly_id 
              and P.`congressman_id` = :congressman_id 
              and P.`title` = :title 
              and P.`from` = :from;
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
            'title' => $title,
            'from' => $from->format('Y-m-d'),
        ]);

        return $this->decorate($statement->fetchObject());
    }

    public function create($data)
    {
        $statement = $this
            ->getDriver()
            ->prepare($this->insertString('President', $data));
        $statement->execute($this->convert($data));
        return $this->getDriver()->lastInsertId();
    }

    public function update($data)
    {
        $statement = $this->getDriver()->prepare(
            $this->updateString('President', $data, "president_id={$data->president_id}")
        );
        $statement->execute($this->convert($data));
        return $statement->rowCount();
    }

    private function decorate($object)
    {
        if (!$object) {
            return null;
        }

        $object->congressman_id = (int) $object->congressman_id;

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
