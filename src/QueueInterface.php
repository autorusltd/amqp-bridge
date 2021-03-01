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
 * QueueInterface
 */
interface QueueInterface
{

    /**
     * Acknowledges the given message
     *
     * @param MessageInterface $message
     *
     * @return void
     */
    public function ack(MessageInterface $message) : void;

    /**
     * Rejects the given message
     *
     * @param MessageInterface $message
     *
     * @return void
     */
    public function reject(MessageInterface $message) : void;

    /**
     * Requeues the given message
     *
     * @param MessageInterface $message
     *
     * @return void
     */
    public function requeue(MessageInterface $message) : void;
}
