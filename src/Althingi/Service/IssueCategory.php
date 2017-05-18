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
use Althingi\Model\IssueCategory as IssueCategoryModel;
use Althingi\Hydrator\IssueCategory as IssueCategoryHydrator;
use Althingi\Model\IssueCategoryAndTime as IssueCategoryAndTimeModel;
use Althingi\Hydrator\IssueCategoryAndTime as IssueCategoryAndTimeHydrator;

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

    /**
     * @param int $assemblyId
     * @param int $issueId
     * @param int $categoryId
     * @return \Althingi\Model\IssueCategory|null
     */
    public function get(int $assemblyId, int $issueId, int $categoryId): ?IssueCategoryModel
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

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new IssueCategoryHydrator())->hydrate($object, new IssueCategoryModel())
            : null;
    }

    /**
     * Create new Issue. This method
     * accepts object from corresponding Form.
     *
     * @param \Althingi\Model\IssueCategory $data
     * @return int
     */
    public function create(IssueCategoryModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Category_has_Issue', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    /**
     * @param \Althingi\Model\IssueCategory $data
     * @return int
     */
    public function update(IssueCategoryModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString(
                'Category_has_Issue',
                $data,
                "category_id={$data->getCategoryId()} and issue_id={$data->getIssueId()} and assembly_id={$data->getAssemblyId()}"
            )
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }

    /**
     * @param int $assemblyId
     * @param int $congressmanId
     * @return \Althingi\Model\IssueCategoryAndTime[]
     */
    public function fetchFrequencyByAssemblyAndCongressman(int $assemblyId, int $congressmanId): array
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

        return array_map(function ($object) {
            return (new IssueCategoryAndTimeHydrator())->hydrate($object, new IssueCategoryAndTimeModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
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
