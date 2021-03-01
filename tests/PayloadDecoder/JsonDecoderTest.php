<?php declare(strict_types=1);

namespace Arus\AMQP\Bridge\Tests\PayloadDecoder;

/**
 * Import classes
 */
use Arus\AMQP\Bridge\PayloadDecoder\Exception\UndecodablePayloadException;
use Arus\AMQP\Bridge\PayloadDecoder\JsonDecoder;
use Arus\AMQP\Bridge\PayloadDecoder\PayloadDecoderInterface;
use Arus\AMQP\Bridge\MessageInterface;
use Arus\AMQP\Bridge\Tests\Stub;
use PHPUnit\Framework\TestCase;

/**
 * JsonDecoderTest
 */
class JsonDecoderTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $decoder = new JsonDecoder();

        $this->assertInstanceOf(PayloadDecoderInterface::class, $decoder);
    }

    /**
     * @return void
     */
    public function testDecode() : void
    {
        $message = new Stub\Message(1, '{"foo":"bar"}');

        $decoder = new JsonDecoder();
        $decodedData = $decoder->decode($message);

        $expectedData = new \stdClass();
        $expectedData->foo = 'bar';

        $this->assertEquals($expectedData, $decodedData);
    }

    /**
     * @return void
     */
    public function testDecodeWithFlags() : void
    {
        $message = new Stub\Message(1, '{"foo":"bar"}');

        $decoder = new JsonDecoder();
        $decodedData = $decoder->decode($message, \JSON_OBJECT_AS_ARRAY);

        $expectedData = ['foo' => 'bar'];

        $this->assertSame($expectedData, $decodedData);
    }

    /**
     * @return void
     */
    public function testDecodeUndecodablePayload() : void
    {
        $message = new Stub\Message(1, '!');

        $decoder = new JsonDecoder();

        $this->expectException(UndecodablePayloadException::class);
        $this->expectExceptionMessage('Syntax error');

        $decoder->decode($message);
    }
}
