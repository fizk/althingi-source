<?php

namespace Althingi\Hydrator;

use Althingi\Model\KindEnum;
use Althingi\Utils\HydratorInterface;

class ParliamentarySessionAgenda implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\ParliamentarySessionAgenda $object
     * @return \Althingi\Model\ParliamentarySessionAgenda
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setItemId($data['item_id'])
            ->setParliamentarySessionId($data['parliamentary_session_id'])
            ->setIssueId($data['issue_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setKind(KindEnum::fromString($data['kind']))
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
     *
     * @param \Althingi\Model\ParliamentarySessionAgenda $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
