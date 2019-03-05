<?php

namespace Althingi\Utils;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPWriter;

class RabbitMQBlackHoleClient extends AMQPStreamConnection
{
    public function __construct()
    {
    }

    public function reconnect()
    {
    }

    public function __clone()
    {
    }

    public function __destruct()
    {
    }

    public function select($sec, $usec = 0)
    {
        return null;
    }

    public function set_close_on_destruct($close = true)
    {
    }

    public function write($data)
    {
    }

    public function get_free_channel_id()
    {
    }

    public function send_content($channel, $class_id, $weight, $body_size, $packed_properties, $body, $pkt = null)
    {
    }

    public function prepare_content($channel, $class_id, $weight, $body_size, $packed_properties, $body, $pkt = null)
    {
        return new AMQPWriter();
    }

    public function channel($channel_id = null)
    {
        return new AMQPChannel(null/*$this->connection*/, $channel_id);
    }

    public function close($reply_code = 0, $reply_text = '', $method_sig = array(0, 0))
    {
        return null;
    }

    public function getSocket()
    {
    }

    public function getIO()
    {
        return $this->io;
    }

    public function set_connection_block_handler($callback)
    {
    }

    public function set_connection_unblock_handler($callback)
    {
    }

    public function isConnected()
    {
        return true;
    }

    public function connectOnConstruct()
    {
        return true;
    }

    public function getServerProperties()
    {
        return [];
    }

    public function getLibraryProperties()
    {
        return self::$LIBRARY_PROPERTIES;
    }

    public static function create_connection($hosts, $options = array())
    {
    }

    public static function validate_host($host)
    {
    }
}
