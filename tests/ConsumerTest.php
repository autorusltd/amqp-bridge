<?php declare(strict_types=1);

namespace Arus\AMQP\Bridge\Tests;

/**
 * Import classes
 */
use Arus\AMQP\Bridge\Exception\UnacknowledgableQueueMessageException;
use Arus\AMQP\Bridge\Consumer;
use Arus\AMQP\Bridge\ConsumerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * ConsumerTest
 */
class ConsumerTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $consumer = new Consumer(new Stub\MessageHandler(null));

        $this->assertInstanceOf(ConsumerInterface::class, $consumer);
    }

    /**
     * @return void
     */
    public function testProcess() : void
    {
        $log = [];

        // *** *** **

        $logger = $this->createMock(LoggerInterface::class);

        $logger->method('debug')->will($this->returnCallback(function ($message) use (&$log) {
            $log['debug'][] = $message;
        }));

        $logger->method('error')->will($this->returnCallback(function ($message) use (&$log) {
            $log['error'][] = $message;
        }));

        // *** *** **

        $queue = new Stub\Queue();
        $queue->requeue(new Stub\Message(1, '{"foo":"bar"}'));
        $queue->requeue(new Stub\Message(2, '{"foo":"bar"}'));
        $queue->requeue(new Stub\Message(3, '!'));
        $queue->requeue(new Stub\Message(4, '{"foo":"baz"}'));
        $queue->requeue(new Stub\Message(5, '{"foo":"bar"}'));

        $queue->setMessageAcknowledgedCallback(function ($message) use (&$log) {
            $log['debug'][] = 'The queue message <' . $message->getId() . '> was acknowledged.';
        });

        $queue->setMessageRejectedCallback(function ($message) use (&$log) {
            $log['debug'][] = 'The queue message <' . $message->getId() . '> was rejected.';
        });

        $queue->setMessageRequeuedCallback(function ($message) use (&$log) {
            $log['debug'][] = 'The queue message <' . $message->getId() . '> was requeued.';
        });

        // *** *** **

        $consumer = new Consumer(new Stub\MessageHandler(function ($message) {
            switch ($message->getId()) {
                case 2:
                    throw new UnacknowledgableQueueMessageException('Some error');
                case 5:
                    throw new \RuntimeException('Some error');
            }
        }));

        $consumer->setLogger($logger);
        $consumer->useJsonSchemaValidator();

        $consumer->setMessageReceivedCallback(function ($message) use (&$log) {
            $log['debug'][] = 'The queue message <' . $message->getId() . '> was received.';
        });

        $consumer->setMessageHandledCallback(function ($message) use (&$log) {
            $log['debug'][] = 'The queue message <' . $message->getId() . '> was handled.';
        });

        // *** *** **

        $consumer->process($queue->dequeue(), $queue);
        $consumer->process($queue->dequeue(), $queue);
        $consumer->process($queue->dequeue(), $queue);
        $consumer->process($queue->dequeue(), $queue);
        $consumer->process($queue->dequeue(), $queue);

        $this->assertSame([
            'debug' => [
                'The queue message <1> was received.',
                'The queue message <1> was handled.',
                'The queue message <1> was acknowledged.',
                'The queue message <2> was received.',
                'The queue message <2> was handled.',
                'The queue message <2> was rejected.',
                'The queue message <2> cannot be acknowledged (reject executed). Some error',
                'The queue message <3> was received.',
                'The queue message <3> was rejected.',
                'The queue message <3> contains undecodable payload (reject executed). Syntax error',
                'The queue message <4> was received.',
                'The queue message <4> was rejected.',
                'The queue message <4> contains invalid payload (reject executed). Payload is not valid.',
                'The queue message <5> was received.',
                'The queue message <5> was handled.',
                'The queue message <5> was requeued.',
            ],
            'error' => [
                'Unexpected error occurred while handling the queue message <5> (requeue executed). Some error',
            ],
        ], $log);

        // requeue test...
        $this->assertCount(1, $queue);
    }
}
