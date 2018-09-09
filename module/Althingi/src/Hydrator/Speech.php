<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;

class Speech implements HydratorInterface
{
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
            ->setCategory(empty($data['category']) ? null : $data['category'])
            ->setPlenaryId($data['plenary_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setIssueId($data['issue_id'])
            ->setCongressmanId($data['congressman_id'])
            ->setCongressmanType($data['congressman_type'])
            ->setFrom($data['from'] ? new \DateTime($data['from']) : null)
            ->setTo($data['to'] ? new \DateTime($data['to']) : null)
            ->setText($data['text'])
            ->setType($data['type'])
            ->setIteration($data['iteration'])
            ->setWordCount(array_key_exists('word_count', $data) ? $data['word_count'] : 0);
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Speech $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
