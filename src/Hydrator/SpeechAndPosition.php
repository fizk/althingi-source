<?php

namespace Althingi\Hydrator;

class SpeechAndPosition extends Speech
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\SpeechAndPosition $object
     * @return \Althingi\Model\SpeechAndPosition $object
     */
    public function hydrate(array $data, $object)
    {
        return parent::hydrate($data, $object)
            ->setPosition($data['position']);
    }
}
