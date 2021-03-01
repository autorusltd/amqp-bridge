<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2021, Anatoly Fenric
 * @license https://github.com/autorusltd/amqp-bridge/blob/master/LICENSE
 * @link https://github.com/autorusltd/amqp-bridge
 */

namespace Arus\AMQP\Bridge;

/**
 * MessageHandlerInterface
 */
interface MessageHandlerInterface
{

    /**
     * Handles the given message
     *
     * @param MessageInterface $message
     *
     * @return void
     *
     * @throws Exception\UnacknowledgableQueueMessageExceptionInterface
     *         If the given message cannot be acknowledged.
     */
    public function handle(MessageInterface $message) : void;
}
