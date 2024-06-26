<?php

namespace Althingi\Injector;

use PhpAmqpLib\Connection\AMQPStreamConnection;

interface QueueAwareInterface
{
    public function setQueue(AMQPStreamConnection $connection);

    public function getQueue(): AMQPStreamConnection;
}
