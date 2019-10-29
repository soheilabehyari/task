<?php


namespace App\Services;


interface QueueInterface
{
    public function publish(String $message, String $queueName);
}
