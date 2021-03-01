<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2021, Anatoly Fenric
 * @license https://github.com/autorusltd/amqp-bridge/blob/master/LICENSE
 * @link https://github.com/autorusltd/amqp-bridge
 */

namespace Arus\AMQP\Bridge\PayloadValidator;

/**
 * Import classes
 */
use Arus\AMQP\Bridge\MessageInterface;

/**
 * PayloadValidatorInterface
 */
interface PayloadValidatorInterface
{

    /**
     * Validates the given message
     *
     * @param MessageInterface $message
     *
     * @return void
     *
     * @throws Exception\InvalidPayloadExceptionInterface
     *         If the given message isn't valid.
     */
    public function validate(MessageInterface $message) : void;
}
