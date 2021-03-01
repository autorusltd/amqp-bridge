<?php declare(strict_types=1);

namespace Arus\AMQP\Bridge\Tests\PayloadValidator;

/**
 * Import classes
 */
use Arus\AMQP\Bridge\PayloadDecoder\Exception\UndecodablePayloadException;
use Arus\AMQP\Bridge\PayloadValidator\Exception\InvalidPayloadException;
use Arus\AMQP\Bridge\PayloadValidator\JsonSchemaValidator;
use Arus\AMQP\Bridge\PayloadValidator\PayloadValidatorInterface;
use Arus\AMQP\Bridge\MessageInterface;
use Arus\AMQP\Bridge\Tests\Stub;
use PHPUnit\Framework\TestCase;

/**
 * JsonSchemaValidatorTest
 */
class JsonSchemaValidatorTest extends TestCase
{

    /**
     * @var string
     */
    private const JSON_SCHEMA_FILE = 'tests/res/json-schema.json';

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $validator = new JsonSchemaValidator(\realpath(self::JSON_SCHEMA_FILE));

        $this->assertInstanceOf(PayloadValidatorInterface::class, $validator);
    }

    /**
     * @return void
     */
    public function testValidate() : void
    {
        $message = new Stub\Message(1, '{"foo":"bar"}');

        $validator = new JsonSchemaValidator(\realpath(self::JSON_SCHEMA_FILE));
        $validator->validate($message);

        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testValidateInvalidPayload() : void
    {
        $message = new Stub\Message(1, '{"foo":"baz"}');

        $validator = new JsonSchemaValidator(\realpath(self::JSON_SCHEMA_FILE));

        $this->expectException(InvalidPayloadException::class);
        $this->expectExceptionMessage('Payload is not valid');

        $validator->validate($message);
    }

    /**
     * @return void
     */
    public function testValidateUndecodablePayload() : void
    {
        $message = new Stub\Message(1, '!');

        $validator = new JsonSchemaValidator(\realpath(self::JSON_SCHEMA_FILE));

        $this->expectException(UndecodablePayloadException::class);
        $this->expectExceptionMessage('Syntax error');

        $validator->validate($message);
    }

    /**
     * @return void
     */
    public function testConstructorWithNonexistentFile() : void
    {
        $jsonSchemaFile = 'nonexistent.file';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to find the JSON schema <' . $jsonSchemaFile . '>.');

        new JsonSchemaValidator($jsonSchemaFile);
    }

    /**
     * @return void
     */
    public function testConstructorWithUnreadableFile() : void
    {
        $jsonSchemaFile = \tempnam(\sys_get_temp_dir(), \uniqid());
        \chmod($jsonSchemaFile, 0200);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to read the JSON schema <' . $jsonSchemaFile . '>.');

        try {
            new JsonSchemaValidator($jsonSchemaFile);
        } catch (\Throwable $e) {
            \unlink($jsonSchemaFile);
            throw $e;
        }
    }
}
