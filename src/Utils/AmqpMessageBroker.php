<?php

namespace Althingi\Utils;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class AmqpMessageBroker implements MessageBrokerInterface
{
    private AMQPStreamConnection $client;
    public function __construct(AMQPStreamConnection $client)
    {
        $this->client = $client;
    }

    public function produce(string $channel = null, string $topic = null, $message)
    {
        $channelName = $channel ?? 'service';
        $amqpChannel = $this->client->channel(1);
        $msg = new AMQPMessage(json_encode($message));

        $amqpChannel->basic_publish($msg, $channelName, $topic);
    }
}
