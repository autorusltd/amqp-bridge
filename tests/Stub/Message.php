<?php declare(strict_types=1);

namespace Arus\AMQP\Bridge\Tests\Stub;

/**
 * Import classes
 */
use Arus\AMQP\Bridge\MessageInterface;

/**
 * Message
 */
final class Message implements MessageInterface
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $payload;

    /**
     * Constructor of the class
     *
     * @param int $id
     * @param string $payload
     */
    public function __construct(int $id, string $payload)
    {
        $this->id = $id;
        $this->payload = $payload;
    }

    /**
     * {@inheritDoc}
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function getPayload() : string
    {
        return $this->payload;
    }
}
