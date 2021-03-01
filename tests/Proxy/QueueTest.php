<?php declare(strict_types=1);

namespace Arus\AMQP\Bridge\Tests\Proxy;

/**
 * Import classes
 */
use Arus\AMQP\Bridge\Proxy\Queue;
use Arus\AMQP\Bridge\QueueInterface;
use Arus\AMQP\Bridge\Tests\Stub;
use PHPUnit\Framework\TestCase;

/**
 * QueueTest
 */
class QueueTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $queue = $this->makeQueue();

        $this->assertInstanceOf(QueueInterface::class, $queue);
    }

    /**
     * @return void
     */
    public function testProxyAck() : void
    {
        $message = new Stub\Message(1, 'foo');
        $args = null;

        $queue = $this->makeAcknowledgableQueue(function () use (&$args) {
            $args = \func_get_args();
        });

        $queue->ack($message);

        $this->assertSame([$message->getId(), \AMQP_NOPARAM], $args);
    }

    /**
     * @return void
     */
    public function testProxyReject() : void
    {
        $message = new Stub\Message(1, 'foo');
        $args = null;

        $queue = $this->makeRejectableQueue(function () use (&$args) {
            $args = \func_get_args();
        });

        $queue->reject($message);

        $this->assertSame([$message->getId(), \AMQP_NOPARAM], $args);
    }

    /**
     * @return void
     */
    public function testProxyRequeue() : void
    {
        $message = new Stub\Message(1, 'foo');
        $args = null;

        $queue = $this->makeRejectableQueue(function () use (&$args) {
            $args = \func_get_args();
        });

        $queue->requeue($message);

        $this->assertSame([$message->getId(), \AMQP_REQUEUE], $args);
    }

    /**
     * @return mixed
     */
    private function makeQueue()
    {
        $basic = $this->createMock('AMQPQueue');

        return new Queue($basic);
    }

    /**
     * @param \Closure $callback
     *
     * @return mixed
     */
    private function makeAcknowledgableQueue(\Closure $callback)
    {
        $basic = $this->createMock('AMQPQueue');
        $basic->method('ack')->will($this->returnCallback($callback));

        return new Queue($basic);
    }

    /**
     * @param \Closure $callback
     *
     * @return mixed
     */
    private function makeRejectableQueue(\Closure $callback)
    {
        $basic = $this->createMock('AMQPQueue');
        $basic->method('reject')->will($this->returnCallback($callback));

        return new Queue($basic);
    }
}
