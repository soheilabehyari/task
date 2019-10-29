<?php
namespace App\Extensions;
use PhpAmqpLib\Connection\AMQPStreamConnection;

trait QueueConnectionChannel
{
    public function setup () {
        $connection = new AMQPStreamConnection(env('QUEUE_HOST'), env('QUEUE_PORT'), env('QUEUE_USER'), env('QUEUE_PASSWORD'), env('QUEUE_VHOST'));
        $channel = $connection->channel();
        return [ $connection, $channel ];
    }
}
