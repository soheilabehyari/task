<?php

namespace App\Services;

use App\Extensions\QueueConnectionChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueService implements QueueInterface
{
    use QueueConnectionChannel;

    public function publish(String $message, String $queueName)
    {
        try {
            $queueMessage = new AMQPMessage($message);
            /* @var AMQPStreamConnection $connection */
            /* @var \PhpAmqpLib\Channel\AMQPChannel $channel */
            [$connection, $channel] = $this->setup();
            $channel->queue_declare($queueName, false, false, false, false);
            $channel->basic_publish($queueMessage, '', $queueName);
            $channel->close();
            $connection->close();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
