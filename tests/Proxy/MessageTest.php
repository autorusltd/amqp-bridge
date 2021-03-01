<?php declare(strict_types=1);

namespace Arus\AMQP\Bridge\Tests\Proxy;

/**
 * Import classes
 */
use Arus\AMQP\Bridge\Proxy\Message;
use Arus\AMQP\Bridge\MessageInterface;
use PHPUnit\Framework\TestCase;

/**
 * MessageTest
 */
class MessageTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $message = $this->makeMessage();

        $this->assertInstanceOf(MessageInterface::class, $message);
    }

    /**
     * @return void
     */
    public function testProxyId() : void
    {
        $message = $this->makeMessage();

        $this->assertSame(1, $message->getId());
    }

    /**
     * @return void
     */
    public function testProxyPayload() : void
    {
        $message = $this->makeMessage();

        $this->assertSame('foo', $message->getPayload());
    }

    /**
     * @return mixed
     */
    private function makeMessage()
    {
        $basic = $this->createMock('AMQPEnvelope');
        $basic->method('getDeliveryTag')->willReturn(1);
        $basic->method('getBody')->willReturn('foo');

        return new Message($basic);
    }
}
