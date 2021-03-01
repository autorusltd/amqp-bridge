<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2021, Anatoly Fenric
 * @license https://github.com/autorusltd/amqp-bridge/blob/master/LICENSE
 * @link https://github.com/autorusltd/amqp-bridge
 */

namespace Arus\AMQP\Bridge\PayloadDecoder;

/**
 * Import classes
 */
use Arus\AMQP\Bridge\PayloadDecoder\Exception\UndecodablePayloadException;
use Arus\AMQP\Bridge\MessageInterface;

/**
 * Import functions
 */
use function defined;
use function json_decode;
use function json_last_error;
use function json_last_error_msg;

/**
 * Import constants
 */
use const JSON_ERROR_NONE;
use const JSON_OBJECT_AS_ARRAY;
use const JSON_THROW_ON_ERROR;

/**
 * JsonDecoder
 */
final class JsonDecoder implements PayloadDecoderInterface
{

    /**
     * {@inheritDoc}
     */
    public function decode(MessageInterface $message, int $flags = 0)
    {
        $payload = $message->getPayload();

        // disable throwing an exception...
        if (defined('JSON_THROW_ON_ERROR')) {
            $flags &= ~JSON_THROW_ON_ERROR;
        }

        // reset a previous error...
        json_decode('{}');

        // decode the message payload with the given flags...
        $assoc = ($flags & JSON_OBJECT_AS_ARRAY) === JSON_OBJECT_AS_ARRAY;
        $result = json_decode($payload, $assoc, 512, $flags);

        if (!(JSON_ERROR_NONE === json_last_error())) {
            throw new UndecodablePayloadException(json_last_error_msg());
        }

        return $result;
    }
}
