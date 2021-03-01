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
 * ConsumerInterface
 */
interface ConsumerInterface
{

    /**
     * Handles the given message from the given queue
     *
     * @param MessageInterface $message
     * @param QueueInterface $queue
     *
     * @return void
     */
    public function process(MessageInterface $message, QueueInterface $queue) : void;
}
