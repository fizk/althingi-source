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
use InvalidArgumentException;

/**
 * Class Issue
 * @package Althingi\Service
 */
class IssueCategory implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    public function get($assemblyId, $issueId, $categoryId)
    {
        $statement = $this->getDriver()->prepare('
            select * from `Category_has_Issue` C
            where C.`assembly_id` = :assembly_id and C.`issue_id` = :issue_id and C.`category_id` = :category_id
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'category_id' => $categoryId
        ]);
        return $this->decorate($statement->fetchObject());
    }

    /**
     * Create new Issue. This method
     * accepts object from corresponding Form.
     *
     * @param object $data
     * @return string
     */
    public function create($data)
    {
        $statement = $this->getDriver()->prepare($this->insertString('Category_has_Issue', $data));
        $statement->execute($this->convert($data));
        return $this->getDriver()->lastInsertId();
    }

    public function update($data)
    {
        $statement = $this->getDriver()->prepare(
            $this->updateString(
                'Category_has_Issue',
                $data,
                "category_id={$data->category_id} and issue_id={$data->issue_id} and assembly_id={$data->assembly_id}"
            )
        );
        $statement->execute($this->convert($data));
        return $statement->rowCount();
    }

    public function fetchFrequencyByAssemblyAndCongressman($assemblyId, $congressmanId)
    {
        $statement = $this->getDriver()->prepare('
            select C.`category_id`, C.`super_category_id`, C.`title`, sum(`speech_sum`) as `time` from (
                select CI.*, TIME_TO_SEC(timediff(SP.`to`, SP.`from`)) as `speech_sum`  
                from `Speech` SP 
                join `Category_has_Issue` CI on (CI.`issue_id` = SP.`issue_id`)
                where SP.`assembly_id` = :assembly_id and SP.`congressman_id` = :congressman_id
            ) as T
            join `Category` C on (C.`category_id` = T.`category_id`)
            group by T.`category_id`
            order by `time` desc;
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
        ]);

        return array_map(function ($item) {
            $item->super_category_id = (int) $item->super_category_id;
            $item->category_id = (int) $item->category_id;
            $item->time = (int) $item->time;

            return $item;
        }, $statement->fetchAll());
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
        $object->issue_id = (int) $object->issue_id;
        $object->category_id = (int) $object->category_id;

        return $object;
    }

}
