<?php

namespace Althingi\Utils;

use RdKafka\Producer;

class KafkaMessageBroker implements MessageBrokerInterface
{
    private Producer $client;

    public function __construct(Producer $client)
    {
        $this->client = $client;
    }

    public function produce(string $channel = null, string $topic = null, $message)
    {
        $topicObject = $this->client->newTopic($topic);
        $topicObject->produce(RD_KAFKA_PARTITION_UA, 0, json_encode($message));
    }

    public function __destruct()
    {
        $timeout_ms = 5;
        $this->client->flush($timeout_ms);
    }
}
