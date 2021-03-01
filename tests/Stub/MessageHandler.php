<?php declare(strict_types=1);

namespace Arus\AMQP\Bridge\Tests\Stub;

/**
 * Import classes
 */
use Arus\AMQP\Bridge\MessageHandlerInterface;
use Arus\AMQP\Bridge\MessageInterface;
use Closure;

/**
 * MessageHandler
 *
 * @JsonSchemaReference("tests/res/json-schema.json")
 */
final class MessageHandler implements MessageHandlerInterface
{

    /**
     * @var null|Closure
     */
    private $callback;

    /**
     * Constructor of the class
     *
     * @param null|Closure $callback
     */
    public function __construct(?Closure $callback)
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(MessageInterface $message) : void
    {
        if ($this->callback instanceof Closure) {
            ($this->callback)($message);
        }
    }
}
