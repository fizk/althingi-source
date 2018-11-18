<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;
use DateTime;

class PlenaryAgenda implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\PlenaryAgenda $object
     * @return \Althingi\Model\PlenaryAgenda
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setItemId($data['item_id'])
            ->setPlenaryId($data['plenary_id'])
            ->setIssueId($data['issue_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setCategory($data['category'])
            ->setIterationType($data['iteration_type'])
            ->setIterationContinue($data['iteration_continue'])
            ->setIterationComment($data['iteration_comment'])
            ->setComment($data['comment'])
            ->setCommentType($data['comment_type'])
            ->setPosedId($data['posed_id'])
            ->setPosed($data['posed'])
            ->setAnswererId($data['answerer_id'])
            ->setAnswerer($data['answerer'])
            ->setCounterAnswererId($data['counter_answerer_id'])
            ->setCounterAnswerer($data['counter_answerer'])
            ->setInstigatorId($data['instigator_id'])
            ->setInstigator($data['instigator']);
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Plenary $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
