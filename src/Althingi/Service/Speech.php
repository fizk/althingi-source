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
 * Class Speech
 * @package Althingi\Service
 */
class Speech implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Create one Speech. Accepts object from
     * corresponding Form.
     *
     * @param $data
     * @return int
     */
    public function create($data)
    {
        $statement = $this->getDriver()->prepare($this->insertString('Speech', $data));
        $statement->execute($this->convert($data));
        return $this->getDriver()->lastInsertId();
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
