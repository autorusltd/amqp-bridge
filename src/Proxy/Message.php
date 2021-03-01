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
use AMQPEnvelope;

/**
 * Wrapper for `AMQPEnvelope` class
 *
 * @link https://github.com/php-amqp/php-amqp/blob/v1.10.0/stubs/AMQPEnvelope.php
 */
final class Message implements MessageInterface
{

    /**
     * @var AMQPEnvelope
     */
    private $envelope;

    /**
     * Constructor of the class
     *
     * @param AMQPEnvelope $envelope
     */
    public function __construct(AMQPEnvelope $envelope)
    {
        $this->envelope = $envelope;
    }

    /**
     * {@inheritDoc}
     */
    public function getId() : int
    {
        return (int) $this->envelope->getDeliveryTag();
    }

    /**
     * {@inheritDoc}
     */
    public function getPayload() : string
    {
        return (string) $this->envelope->getBody();
    }
}
