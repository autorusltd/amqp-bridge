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
 * MessageInterface
 */
interface MessageInterface
{

    /**
     * Gets the message ID
     *
     * @return int
     */
    public function getId() : int;

    /**
     * Gets the message payload
     *
     * @return string
     */
    public function getPayload() : string;
}
