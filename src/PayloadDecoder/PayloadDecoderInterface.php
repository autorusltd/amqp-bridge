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
use Arus\AMQP\Bridge\MessageInterface;

/**
 * PayloadDecoderInterface
 */
interface PayloadDecoderInterface
{

    /**
     * Decodes the given message
     *
     * @param MessageInterface $message
     *
     * @return mixed
     *
     * @throws Exception\UndecodablePayloadExceptionInterface
     *         If the given message isn't decodable.
     */
    public function decode(MessageInterface $message);
}
