<?php
namespace Althingi\Store;

use Althingi\Lib\StoreAwareInterface;
use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Lib\Transformer;
use MongoDB\Database;

class Issue implements StoreAwareInterface
{
    /** @var \MongoDB\Database */
    private $database;

    /**
     * @param int $assemblyId
     * @param int $issueId
     * @param string $category
     * @return Model\IssueProperties
     */
    public function get(int $assemblyId, int $issueId, string $category = 'A'): Model\IssueProperties
    {
        $document = $this->getStore()->issue->findOne([
            'issue.assembly_id' => $assemblyId,
            'issue.issue_id' => $issueId,
            'issue.category' => $category,
        ]);

        return $this->hydrateIssue($document);
    }

    /**
     * @param int $assemblyId
     * @param array $types
     * @param array $kinds
     * @param array $categories
     * @return array
     */
    public function fetchByAssembly(
        int $assemblyId,
        array $types = [],
        array $kinds = [],
        array $categories = ['A', 'B']
    ): array {
        $criteria = array_merge(
            ['issue.assembly_id' => $assemblyId],
            ['issue.category' => ['$in' => $categories]],
            count($types) ? ['issue.type' => ['$in' => $types]] : [],
            count($kinds) ? ['categories.category_id' => ['$in' => $kinds]] : []
        );

        $documents = $this->getStore()->issue->aggregate([
            ['$match' => $criteria],
            ['$skip' => 0],
            ['$limit' => 200]
        ]);

        return array_map(function ($object) {
            return $this->hydrateIssue($object);
        }, iterator_to_array($documents));
    }

    /**
     * @param int $assemblyId
     * @param array $types
     * @param array $kinds
     * @param array $categories
     * @return int
     */
    public function countByAssembly(
        int $assemblyId,
        array $types = [],
        array $kinds = [],
        array $categories = ['A', 'B']
    ): int {
        $criteria = array_merge(
            ['issue.assembly_id' => $assemblyId],
            ['issue.category' => ['$in' => $categories]],
            count($types) ? ['issue.type' => ['$in' => $types]] : [],
            count($kinds) ? ['categories.category_id' => ['$in' => $kinds]] : []
        );

        $documents = $this->getStore()->issue->aggregate([
            ['$match' => $criteria],
            ['$count' => 'total']
        ]);

        return array_reduce(iterator_to_array($documents), function ($carry, $item) {
            return $carry + $item->total;
        }, 0);
    }

    /**
     * @param Database $database
     * @return $this
     */
    public function setStore(Database $database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * @return Database
     */
    public function getStore(): Database
    {
        return $this->database;
    }

    /**
     * @param $object
     * @return Model\IssueProperties|null
     */
    private function hydrateIssue($object): ?Model\IssueProperties
    {
        if (! $object) {
            return null;
        }
        $issue = (new Hydrator\Issue())->hydrate((array) $object->issue, new Model\Issue());

        $issue->setGoal(Transformer::htmlToMarkdown($issue->getGoal()));
        $issue->setMajorChanges(Transformer::htmlToMarkdown($issue->getMajorChanges()));
        $issue->setChangesInLaw(Transformer::htmlToMarkdown($issue->getChangesInLaw()));
        $issue->setCostsAndRevenues(Transformer::htmlToMarkdown($issue->getCostsAndRevenues()));
        $issue->setAdditionalInformation(Transformer::htmlToMarkdown($issue->getAdditionalInformation()));
        $issue->setDeliveries(Transformer::htmlToMarkdown($issue->getDeliveries()));

        $issueProperties = (new Model\IssueProperties())
            ->setIssue($issue)
            ->setGovernmentIssue(isset($object->isGovernmentIssue) ? $object->isGovernmentIssue : false)
            ->setCategories(isset($object->categories) ? array_map(function ($category) {
                return (new Hydrator\Category())->hydrate((array) $category, new Model\Category());
            }, (array) $object->categories) : [])
            ->setSuperCategory(isset($object->superCategories) ? array_map(function ($category) {
                return (new Hydrator\SuperCategory())->hydrate((array) $category, new Model\SuperCategory());
            }, (array) $object->superCategories) : [])
            ->setDate((isset($object->date) && $object->date !== null) ? $object->date->toDateTime() : null)
            ->setProponents(array_map(function ($proponent) {
                return (new Model\CongressmanPartyProperties())
                    ->setConstituency(
                        (new Hydrator\Constituency())
                            ->hydrate((array) $proponent->constituency, new Model\Constituency())
                    )
                    ->setCongressman(
                        (new Hydrator\Congressman())
                            ->hydrate((array) $proponent->congressman, new Model\Congressman())
                    )
                    ->setParty(
                        (new Hydrator\Party())
                            ->hydrate((array) $proponent->party, new Model\Party())
                    );
            }, isset($object->proponents) ? (array) $object->proponents : []))
        ;

        return $issueProperties;
    }
}
