<?php
namespace Althingi\Store;

use Althingi\Injector\StoreAwareInterface;
use Althingi\Utils\Transformer;
use Althingi\Model;
use Althingi\Hydrator;
use MongoDB\Database;

class Speech implements StoreAwareInterface
{
    /** @var \MongoDB\Database */
    private $database;

    /**
     * Get speeches for a give issue. The array returned
     * will not contain more words than $words.
     *
     * @param $assemblyId
     * @param $issueId
     * @param $category
     * @param int|null $offset
     * @param int|null $size
     * @param int|null $words
     * @return \Althingi\Model\SpeechCongressmanProperties[]
     */
    public function fetchByIssue(
        $assemblyId,
        $issueId,
        $category,
        ?int $offset = 0,
        ?int $size = null,
        ?int $words = 1500
    ) {
        /** @var  $documents \MongoDB\Driver\Cursor */
        $documents = $this->getStore()->speech->aggregate([
            ['$match' => [
                'assembly.assembly_id' => (int)$assemblyId,
                'speech.issue_id' => (int)$issueId,
                'speech.category' => strtoupper($category),
            ]],[
                '$skip' => $offset,
            ]
        ]);

        $count = 0;
        $result = [];

        foreach ($documents as $state) {
            $result[] = $state;
            if ($count > $words) {
                break;
            }
            $count += (int)$state['speech']['word_count'];
        }

        return array_map(function ($speech) {
            $speechModel = (new Hydrator\Speech())->hydrate((array)$speech['speech'], new Model\Speech());
            $speechModel->setText(Transformer::speechToMarkdown($speechModel->getText()));

            $congressmanPartyProperties = (new Model\CongressmanPartyProperties())
                ->setCongressman(
                    (new Hydrator\Congressman())->hydrate((array)$speech['congressman'], new Model\Congressman())
                )
                ->setParty(
                    (new Hydrator\Party())->hydrate((array)$speech['congressman']['party'], new Model\Party())
                )
                ->setConstituency(
                    (new Hydrator\Constituency())
                        ->hydrate((array)$speech['congressman']['constituency'], new Model\Constituency())
                );

            return (new Model\SpeechCongressmanProperties())
                ->setCongressman($congressmanPartyProperties)
                ->setSpeech($speechModel);
        }, $result);
    }

    /**
     * Count all speeches for a given issue.
     *
     * @param $assemblyId
     * @param $issueId
     * @param $category
     * @return mixed
     */
    public function countByIssue($assemblyId, $issueId, $category)
    {
        /** @var  $documents \MongoDB\Driver\Cursor */
        $documents = $this->getStore()->speech->aggregate([
            ['$match' => [
                'assembly.assembly_id' => (int)$assemblyId,
                'speech.issue_id' => (int)$issueId,
                'speech.category' => strtoupper($category),
            ]],
            [ '$group' => [ '_id' => null, 'count' => [ '$sum' => 1 ] ] ],
            [ '$project' => [ '_id' => 0 ] ]
        ]);

        return array_reduce($documents->toArray(), function ($carry, $item) {
            return $carry + $item['count'];
        }, 0);
    }

    public function fetchFrequencyByAssembly(int $assemblyId)
    {
        $documents = $this->getStore()->speech->aggregate([
            [
                '$match' => [
                    'issue.assembly_id' => $assemblyId
                ],
            ],
            [
                '$group' => [
                    '_id' => [
                        'month' => ['$month' => '$speech.from'],
                        'day' => ['$dayOfMonth' => '$speech.from'],
                        'year' => ['$year' => '$speech.from' ],
                    ],
                    'total' => ['$sum' => '$time']
                ],
            ],
            [
                '$project' => [
                    'count' => '$total',
                    'date' => [
                        '$dateFromParts' => [
                            'year' => '$_id.year',
                            'month' => '$_id.month',
                            'day' => '$_id.day',
                            'hour' => 0,
                            'minute' => 0,
                            'second' => 0,
                            'timezone' => '+00:00',
                        ],
                    ],
                ]
            ],
            [
                '$sort' => ['date' => 1]
            ],
        ]);

        return array_map(function ($vote) {
            return (new Hydrator\DateAndCount())->hydrate(
                array_merge((array) $vote, ['date' => $vote->date ? $vote->date->toDateTime() : null]),
                new Model\DateAndCount()
            );
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
}
