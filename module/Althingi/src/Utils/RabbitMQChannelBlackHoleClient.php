<?php

namespace Althingi\Utils;

use PhpAmqpLib\Channel\AMQPChannel;

class RabbitMQChannelBlackHoleClient extends AMQPChannel
{

    public function __construct($connection, $channel_id = null, $auto_decode = true)
    {
    }

    public function close($reply_code = 0, $reply_text = '', $method_sig = [0, 0])
    {
    }

    public function flow($active)
    {
    }

    public function access_request(  // phpcs:ignore
        $realm,
        $exclusive = false,
        $passive = false,
        $active = false,
        $write = false,
        $read = false
    ) {
    }

    public function exchange_declare(  // phpcs:ignore
        $exchange,
        $type,
        $passive = false,
        $durable = false,
        $auto_delete = true,
        $internal = false,
        $nowait = false,
        $arguments = [],
        $ticket = null
    ) {
    }

    public function exchange_delete(  // phpcs:ignore
        $exchange,
        $if_unused = false,
        $nowait = false,
        $ticket = null
    ) {
    }

    public function exchange_bind(  // phpcs:ignore
        $destination,
        $source,
        $routing_key = '',
        $nowait = false,
        $arguments = [],
        $ticket = null
    ) {
    }

    public function exchange_unbind( // phpcs:ignore
        $destination,
        $source,
        $routing_key = '',
        $nowait = false,
        $arguments = [],
        $ticket = null
    ) {
    }

    public function queue_bind(  // phpcs:ignore
        $queue,
        $exchange,
        $routing_key = '',
        $nowait = false,
        $arguments = [],
        $ticket = null
    ) {
    }

    public function queue_unbind( // phpcs:ignore
        $queue,
        $exchange,
        $routing_key = '',
        $arguments = [],
        $ticket = null
    ) {
    }

    public function queue_declare( // phpcs:ignore
        $queue = '',
        $passive = false,
        $durable = false,
        $exclusive = false,
        $auto_delete = true,
        $nowait = false,
        $arguments = [],
        $ticket = null
    ) {
    }

    public function queue_delete(  // phpcs:ignore
        $queue = '',
        $if_unused = false,
        $if_empty = false,
        $nowait = false,
        $ticket = null
    ) {
    }

    public function queue_purge($queue = '', $nowait = false, $ticket = null) // phpcs:ignore
    {
    }

    public function basic_ack($delivery_tag, $multiple = false) // phpcs:ignore
    {
    }

    public function basic_nack($delivery_tag, $multiple = false, $requeue = false) // phpcs:ignore
    {
    }

    public function basic_cancel($consumer_tag, $nowait = false, $noreturn = false) // phpcs:ignore
    {
    }

    public function basic_consume( // phpcs:ignore
        $queue = '',
        $consumer_tag = '',
        $no_local = false,
        $no_ack = false,
        $exclusive = false,
        $nowait = false,
        $callback = null,
        $ticket = null,
        $arguments = []
    ) {
    }

    public function basic_get($queue = '', $no_ack = false, $ticket = null) // phpcs:ignore
    {
    }

    public function basic_publish( // phpcs:ignore
        $msg,
        $exchange = '',
        $routing_key = '',
        $mandatory = false,
        $immediate = false,
        $ticket = null
    ) {
    }

    public function batch_basic_publish( // phpcs:ignore
        $msg,
        $exchange = '',
        $routing_key = '',
        $mandatory = false,
        $immediate = false,
        $ticket = null
    ) {
    }

    public function publish_batch() // phpcs:ignore
    {
    }

    public function basic_qos($prefetch_size, $prefetch_count, $a_global) // phpcs:ignore
    {
    }

    public function basic_recover($requeue = false) // phpcs:ignore
    {
    }

    public function basic_reject($delivery_tag, $requeue) // phpcs:ignore
    {
    }

    public function tx_commit() // phpcs:ignore
    {
    }

    public function tx_rollback() // phpcs:ignore
    {
    }

    public function confirm_select($nowait = false) // phpcs:ignore
    {
    }

    public function confirm_select_ok($reader) // phpcs:ignore
    {
    }

    public function wait_for_pending_acks($timeout = 0) // phpcs:ignore
    {
    }

    public function wait_for_pending_acks_returns($timeout = 0) // phpcs:ignore
    {
    }

    public function tx_select() // phpcs:ignore
    {
    }

    public function set_return_listener($callback) // phpcs:ignore
    {
    }

    public function set_nack_handler($callback) // phpcs:ignore
    {
    }

    public function set_ack_handler($callback) // phpcs:ignore
    {
    }
}
