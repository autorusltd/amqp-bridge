<?php declare(strict_types=1);

namespace Arus\AMQP\Bridge\Tests\Stub;

/**
 * Import classes
 */
use Arus\AMQP\Bridge\MessageInterface;
use Arus\AMQP\Bridge\QueueInterface;
use Closure;
use Countable;
use SplQueue;

/**
 * Queue
 */
final class Queue implements QueueInterface, Countable
{

    /**
     * @var SplQueue
     */
    private $queue;

    /**
     * @var null|Closure
     */
    private $messageAcknowledgedCallback = null;

    /**
     * @var null|Closure
     */
    private $messageRejectedCallback = null;

    /**
     * @var null|Closure
     */
    private $messageRequeuedCallback = null;

    /**
     * Constructor of the class
     */
    public function __construct()
    {
        $this->queue = new SplQueue();
    }

    /**
     * @param Closure $messageAcknowledgedCallback
     *
     * @return void
     */
    public function setMessageAcknowledgedCallback(Closure $messageAcknowledgedCallback) : void
    {
        $this->messageAcknowledgedCallback = $messageAcknowledgedCallback;
    }

    /**
     * @param Closure $messageRejectedCallback
     *
     * @return void
     */
    public function setMessageRejectedCallback(Closure $messageRejectedCallback) : void
    {
        $this->messageRejectedCallback = $messageRejectedCallback;
    }

    /**
     * @param Closure $messageRequeuedCallback
     *
     * @return void
     */
    public function setMessageRequeuedCallback(Closure $messageRequeuedCallback) : void
    {
        $this->messageRequeuedCallback = $messageRequeuedCallback;
    }

    /**
     * {@inheritDoc}
     */
    public function ack(MessageInterface $message) : void
    {
        if ($this->messageAcknowledgedCallback instanceof Closure) {
            ($this->messageAcknowledgedCallback)($message);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function reject(MessageInterface $message) : void
    {
        if ($this->messageRejectedCallback instanceof Closure) {
            ($this->messageRejectedCallback)($message);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function requeue(MessageInterface $message) : void
    {
        if ($this->messageRequeuedCallback instanceof Closure) {
            ($this->messageRequeuedCallback)($message);
        }

        $this->queue->enqueue($message);
    }

    /**
     * Dequeues the next message
     *
     * @return null|MessageInterface
     */
    public function dequeue() : ?MessageInterface
    {
        if ($this->queue->isEmpty()) {
            return null;
        }

        return $this->queue->dequeue();
    }

    /**
     * Gets the number of messages in the queue
     *
     * @return int
     */
    public function count() : int
    {
        return $this->queue->count();
    }
}
