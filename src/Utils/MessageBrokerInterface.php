<?php

namespace Althingi\Utils;

interface MessageBrokerInterface
{
    public function produce(string $channel = null, string $topic = null, $message);
}
