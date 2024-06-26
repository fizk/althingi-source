<?php

namespace Althingi\Hydrator;

use Althingi\Model\KindEnum;
use Laminas\Hydrator\HydratorInterface;

class Speech implements HydratorInterface
{
    use HydrateDate;

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Speech $object
     * @return \Althingi\Model\Speech $object
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setSpeechId($data['speech_id'])
            ->setKind($data['kind'] ? KindEnum::fromString($data['kind']) : null)
            ->setPlenaryId($data['plenary_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setIssueId($data['issue_id'])
            ->setCongressmanId($data['congressman_id'])
            ->setCongressmanType($data['congressman_type'])
            ->setFrom(array_key_exists('from', $data) ? $this->hydrateDate($data['from']) : null)
            ->setTo(array_key_exists('to', $data) ? $this->hydrateDate($data['to']) : null)
            ->setText($data['text'])
            ->setType($data['type'])
            ->setIteration($data['iteration'])
            ->setWordCount(array_key_exists('word_count', $data) ? $data['word_count'] : 0)
            ->setValidated(array_key_exists('validated', $data) ? $data['validated'] : true);
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Speech $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
