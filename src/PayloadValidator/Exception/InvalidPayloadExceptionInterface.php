<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2021, Anatoly Fenric
 * @license https://github.com/autorusltd/amqp-bridge/blob/master/LICENSE
 * @link https://github.com/autorusltd/amqp-bridge
 */

namespace Arus\AMQP\Bridge\PayloadValidator\Exception;

/**
 * Import classes
 */
use Throwable;

/**
 * InvalidPayloadExceptionInterface
 */
interface InvalidPayloadExceptionInterface extends Throwable
{

    /**
     * Gets a list of errors
     *
     * @return array Any structure...
     */
    public function getErrors() : array;
}
