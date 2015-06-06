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

class Assembly implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Get one Assembly.
     *
     * @param $id
     * @return null|object
     */
    public function get($id)
    {
        $statement = $this->getDriver()->prepare("
            select * from `Assembly` where assembly_id = :id
        ");
        $statement->execute(['id' => $id]);
        return $statement->fetchObject() ? : null ;
    }

    /**
     * Get all Assemblies.
     *
     * @param int $from
     * @param int $to
     * @return array
     */
    public function fetchAll($from, $to)
    {
        $statement = $this->getDriver()->prepare("
            select * from `Assembly` A order by A.`from` desc
            limit {$from}, {$to}
        ");
        $statement->execute();
        return $statement->fetchAll();
    }

    public function count()
    {
        $statement = $this->getDriver()->prepare("
            select count(*) from `Assembly` A
        ");
        $statement->execute();
        return (int) $statement->fetchColumn(0);
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
            ->prepare($this->insertString('Assembly', $data));
        $statement->execute($this->convert($data));
        return $statement->rowCount();
    }

    /**
     * Update one entry.
     *
     * @param object $data
     * @return int affected rows
     */
    public function update($data)
    {
        $statement = $this
            ->getDriver()
            ->prepare(
                $this->updateString('Assembly', $data, "assembly_id={$data->assembly_id}")
            );
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
}
