<?php
namespace Althingi\Store;

use Althingi\Injector\StoreAwareInterface;
use Althingi\Model;
use Althingi\Hydrator;
use MongoDB\Database;

class Document implements StoreAwareInterface
{
    /** @var \MongoDB\Database */
    private $database;

    /**
     * @param int $assemblyId
     * @param int $issueId
     * @return \Althingi\Model\DocumentProperties[]
     */
    public function fetchByIssue(int $assemblyId, int $issueId): array
    {
        $documents = $this->getStore()->document->find([
            'assembly.assembly_id' => $assemblyId,
            'document.issue_id' => $issueId,
            'document.category' => 'A',
        ], [
            'sort' => ['document.date' => 1]
        ]);

        return array_map(function ($document) {
            $doc = (array)$document['document'];
            $pro = (array)$document['proponents'];
            $vot = (array)$document['votes'];


            return (new Model\DocumentProperties())
                ->setDocument((new Hydrator\Document())->hydrate($doc, new Model\Document()))
                ->setVotes(array_map(function ($vote) {
                    return (new Hydrator\Vote())->hydrate((array)$vote, new Model\Vote());
                }, $vot))
                ->setProponents(array_map(function ($congressman) {

                    return (new Model\ProponentPartyProperties())
                        ->setOrder($congressman['order'])
                        ->setMinister($congressman['minister'])
                        ->setCongressman(
                            (new Hydrator\Congressman())
                                ->hydrate((array)$congressman['congressman'], new Model\Congressman())
                        )
                        ->setParty(
                            (new Hydrator\Party())
                                ->hydrate((array)$congressman['congressman']['party'], new Model\Party())
                        )
                        ->setConstituency(
                            (new Hydrator\Constituency())
                                ->hydrate((array)$congressman['congressman']['constituency'], new Model\Constituency())
                        );
                }, $pro));
        }, $documents->toArray());
    }

    public function setStore(Database $database)
    {
        $this->database = $database;
        return $this;
    }

    public function getStore(): Database
    {
        return $this->database;
    }
}
