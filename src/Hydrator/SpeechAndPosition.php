<?php

namespace Althingi\Hydrator;

class SpeechAndPosition extends Speech
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\SpeechAndPosition $object
     * @return \Althingi\Model\SpeechAndPosition
     */
    public function hydrate(array $data, object $object): object
    {
        return parent::hydrate($data, $object)
            // FIXME this method doesn't exist
            ->setPosition($data['position'])
        ;
    }
}
