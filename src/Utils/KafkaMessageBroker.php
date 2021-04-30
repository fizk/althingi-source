<?php

namespace Althingi\Utils;

use RdKafka\Conf;
use RdKafka\Producer;

class KafkaMessageBroker implements MessageBrokerInterface
{
    private Producer $producer;

    public function __construct(string $brokers)
    {
        $conf = new Conf();
        $conf->set('log_level', (string) LOG_DEBUG);
        $conf->set('debug', 'all');
        $this->producer = new Producer($conf);
        $this->producer->addBrokers($brokers);
    }
    public function produce(string $channel = null, string $topic = null, $message)
    {
        $this->producer
            ->newTopic($topic)
            ->produce(RD_KAFKA_PARTITION_UA, 0, json_encode($message));
    }

    public function __destruct()
    {
        $timeout_ms = 10;
        $this->producer->flush($timeout_ms);
    }
}
