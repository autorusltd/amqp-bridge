<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2021, Anatoly Fenric
 * @license https://github.com/autorusltd/amqp-bridge/blob/master/LICENSE
 * @link https://github.com/autorusltd/amqp-bridge
 */

namespace Arus\AMQP\Bridge\Proxy;

/**
 * Import classes
 */
use Arus\AMQP\Bridge\MessageInterface;
use Arus\AMQP\Bridge\QueueInterface;
use AMQPQueue;

/**
 * Import constants
 */
use const AMQP_NOPARAM;
use const AMQP_REQUEUE;

/**
 * Wrapper for `AMQPQueue` class
 *
 * @link https://github.com/php-amqp/php-amqp/blob/v1.10.0/stubs/AMQPQueue.php
 */
final class Queue implements QueueInterface
{

    /**
     * @var AMQPQueue
     */
    private $queue;

    /**
     * Constructor of the class
     *
     * @param AMQPQueue $queue
     */
    public function __construct(AMQPQueue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * {@inheritDoc}
     */
    public function ack(MessageInterface $message) : void
    {
        $this->queue->ack($message->getId(), AMQP_NOPARAM);
    }

    /**
     * {@inheritDoc}
     */
    public function reject(MessageInterface $message) : void
    {
        $this->queue->reject($message->getId(), AMQP_NOPARAM);
    }

    /**
     * {@inheritDoc}
     */
    public function requeue(MessageInterface $message) : void
    {
        $this->queue->reject($message->getId(), AMQP_REQUEUE);
    }
}
