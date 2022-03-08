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
        $this->client->poll(0);
    }

    public function __destruct()
    {
        for ($flushRetries = 0; $flushRetries < 10; $flushRetries++) {
            $result = $this->client->flush(10000);
            if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
                break;
            }
        }

        if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
            throw new \RuntimeException('Was unable to flush, messages might be lost!');
        }
    }
}
