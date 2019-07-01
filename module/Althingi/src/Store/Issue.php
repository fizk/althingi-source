<?php
namespace Althingi\Store;

use Althingi\Injector\StoreAwareInterface;
use Althingi\Utils\Transformer;
use Althingi\Model;
use Althingi\Hydrator;
use MongoDB\Database;

class Issue implements StoreAwareInterface
{
    /** @var \MongoDB\Database */
    private $database;

    /**
     * @param int $assemblyId
     * @param int $issueId
     * @param string $category
     * @return Model\IssueProperties | null
     */
    public function get(int $assemblyId, int $issueId, string $category = 'A'): ?Model\IssueProperties
    {
        $issue = $this->getStore()->issue->findOne([
            'issue.assembly_id' => $assemblyId,
            'issue.issue_id' => $issueId,
            'issue.category' => $category,
        ]);

        return $this->hydrateIssue($issue);
    }

    /**
     * @param int $assemblyId
     * @param int $offset
     * @param int $size
     * @param array $types
     * @param array $kinds
     * @param array $categories
     * @return array
     */
    public function fetchByAssembly(
        int $assemblyId,
        ?int $offset = 0,
        ?int $size = 0,
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
        $size = $size ? : 25;

        $issues = $this->getStore()->issue->aggregate([
            ['$match' => $criteria],
            ['$skip' => $offset],
            ['$limit' => $size],
            ['$sort' => ['issue.issue_id' => 1]],
        ]);

        return array_map(function ($object) {
            return $this->hydrateIssue($object);
        }, iterator_to_array($issues));
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
     * Fetch all issues where a proponent is in a given party.
     *
     * @param int $assemblyId
     * @param int $partyId
     * @param array $types
     * @param int|null $offset
     * @param int|null $size
     * @return array
     */
    public function fetchByParty(
        int $assemblyId,
        int $partyId,
        array $types = [],
        ?int $offset = 0,
        ?int $size = 0
    ) {
        $size = $size ? : 25;
        $criteria = array_merge(
            ['issue.assembly_id' => $assemblyId],
            ['proponents.congressman.party.party_id' => $partyId],
            count($types) ? ['issue.type' => ['$in' => $types]] : []
        );
        $issues = $this->getStore()->issue->aggregate([
            ['$match' => $criteria],
            ['$skip' => $offset],
            ['$limit' => $size],
            ['$sort' => ['issue.issue_id' => 1]],
        ]);

        return array_map(function ($object) {
            return $this->hydrateIssue($object);
        }, iterator_to_array($issues));
    }

    /**
     * Count all issues where a proponent is in a given party.
     *
     * @param int $assemblyId
     * @param int $partyId
     * @param array $types
     * @return int
     */
    public function countByParty(
        int $assemblyId,
        int $partyId,
        array $types = []
    ): int {
        $criteria = array_merge(
            ['issue.assembly_id' => $assemblyId],
            ['proponents.congressman.party.party_id' => $partyId],
            count($types) ? ['issue.type' => ['$in' => $types]] : []
        );
        $issues = $this->getStore()->issue->aggregate([
            ['$match' => $criteria],
            ['$count' => 'total'],
        ]);

        return array_reduce(iterator_to_array($issues), function ($carry, $item) {
            return $carry + $item->total;
        }, 0);
    }

    public function fetchGovernmentBillStatisticsByAssembly(int $assemblyId)
    {
        $documents = $this->getStore()->issue->aggregate([
            [
                '$match' => [
                    'issue.assembly_id' => $assemblyId,
                    'government_issue' => true,
                ]
            ],
            [
                '$group' => [
                    '_id' => '$issue.status',
                    'count' => [ '$sum' => 1 ]
                ]
            ],
            [
                '$project' => [
                    'status' => '$_id',
                    'count' => '$count'
                ]
            ]
        ]);

        return array_map(function ($object) {
            return (new Hydrator\IssueTypeStatus())->hydrate((array) $object, new Model\IssueTypeStatus());
        }, iterator_to_array($documents));
    }

    public function fetchNonGovernmentBillStatisticsByAssembly(int $assemblyId)
    {
        $documents = $this->getStore()->issue->aggregate([
            [
                '$match' => [
                    'issue.assembly_id' => $assemblyId,
                    'issue.type' => 'l',
                ]
            ],
            [
                '$group' => [
                    '_id' => '$issue.status',
                    'count' => [ '$sum' => 1 ]
                ]
            ],
            [
                '$project' => [
                    'status' => '$_id',
                    'count' => '$count'
                ]
            ]
        ]);

        return array_map(function ($object) {
            return (new Hydrator\IssueTypeStatus())->hydrate((array) $object, new Model\IssueTypeStatus());
        }, iterator_to_array($documents));
    }

    public function fetchProposalStatisticsByAssembly(int $assemblyId)
    {
        $documents = $this->getStore()->issue->aggregate([
            [
                '$match' => [
                    'issue.assembly_id' => $assemblyId,
                    'issue.type' => ['$in' => ['v', 'a', 'f']],
                ]
            ],
            [
                '$group' => [
                    '_id' => '$issue.status',
                    'count' => [ '$sum' => 1 ]
                ]
            ],
            [
                '$project' => [
                    'status' => '$_id',
                    'count' => '$count'
                ]
            ]
        ]);

        return array_map(function ($object) {
            return (new Hydrator\IssueTypeStatus())->hydrate((array) $object, new Model\IssueTypeStatus());
        }, iterator_to_array($documents));
    }

    public function fetchCountByCategory(int $assemblyId)
    {
        $documents = $this->getStore()->issue->aggregate([
            [
                '$match' => [
                    'issue.assembly_id' => $assemblyId,
                ],
            ],
            [
                '$group' => [
                    '_id' => [
                        'type' => '$issue.type',
                        'type_name' => '$issue.type_name',
                        'type_subname' => '$issue.type_subname',
                        'category' => '$issue.category',
                    ],
                    'count' => [ '$sum' => 1 ]
                ],
            ],
            [
                '$project' => [
                    'type' => '$_id.type',
                    'type_name' => '$_id.type_name',
                    'type_subname' => '$_id.type_subname',
                    'category' => '$_id.category',
                    'count' => '$count'
                ],
            ],
            [
                '$sort' => ['category' => 1, 'count' => -1]
            ]
        ]);

        return array_map(function ($object) {
            return (new Hydrator\AssemblyStatus())->hydrate((array) $object, new Model\AssemblyStatus());
        }, iterator_to_array($documents));
    }

    public function fetchByAssemblyAndSpeechTime(int $assemblyId, int $size, int $order, array $categories = [])
    {
        $documents = $this->getStore()->issue->find([
            'issue.assembly_id' => $assemblyId,
        ], [
            'sort' => ['speech_time' => -1],
            'limit' => $size
        ]);

        return array_map(function ($object) {
            return $this->hydrateIssue($object);
        }, iterator_to_array($documents));
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
            ->setSpeechCount($object->speech_count)
            ->setSpeechTime($object->speech_time)
            ->setIssue($issue)
            ->setGovernmentIssue(isset($object->government_issue) ? $object->government_issue : false)
            ->setCategories(isset($object->categories) ? array_map(function ($category) {
                return (new Hydrator\Category())->hydrate((array) $category, new Model\Category());
            }, (array) $object->categories) : [])
            ->setSuperCategory(isset($object->super_categories) ? array_map(function ($category) {
                return (new Hydrator\SuperCategory())->hydrate((array) $category, new Model\SuperCategory());
            }, (array) $object->super_categories) : [])
            ->setDate((isset($object->date) && $object->date !== null) ? $object->date->toDateTime() : null)
            ->setLinks(array_map(function ($link) {
                return (new Hydrator\Link())->hydrate((array) $link, new Model\Link());
            }, isset($object->link) ? (array) $object->link : []))
            ->setProponents(array_map(function ($proponent) {
                return (new Model\ProponentPartyProperties())
                    ->setOrder($proponent->order)
                    ->setMinister($proponent->minister)
                    ->setConstituency(
                        $proponent->congressman->constituency ? (new Hydrator\Constituency())
                            ->hydrate((array) $proponent->congressman->constituency, new Model\Constituency())
                            : null
                    )
                    ->setCongressman(
                        (new Hydrator\Congressman())
                            ->hydrate((array) $proponent->congressman, new Model\Congressman())
                    )
                    ->setParty(
                        $proponent->congressman->party ? (new Hydrator\Party())
                            ->hydrate((array) $proponent->congressman->party, new Model\Party())
                            : null
                    );
            }, isset($object->proponents) ? (array) $object->proponents : []))
            ->setSpeakers(array_map(function ($speaker) {
                return (new Model\CongressmanPartyValueProperties())
                    ->setValue($speaker->time)
                    ->setConstituency(
                        $speaker->congressman->constituency ? (new Hydrator\Constituency())
                            ->hydrate((array) $speaker->congressman->constituency, new Model\Constituency())
                            : null
                    )
                    ->setParty(
                        $speaker->congressman->party ? (new Hydrator\Party())
                            ->hydrate((array) $speaker->congressman->party, new Model\Party())
                            : null
                    )
                    ->setCongressman(
                        $speaker->congressman ? (new Hydrator\Congressman())
                            ->hydrate((array) $speaker->congressman, new Model\Congressman())
                            : null
                    );
            }, isset($object->speakers) ? (array) $object->speakers : []));
        ;

        return $issueProperties;
    }
}
