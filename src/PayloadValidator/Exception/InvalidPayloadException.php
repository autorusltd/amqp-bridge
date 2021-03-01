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
use RuntimeException;

/**
 * InvalidPayloadException
 */
final class InvalidPayloadException extends RuntimeException implements InvalidPayloadExceptionInterface
{

    /**
     * @var array
     */
    private $errors;

    /**
     * Constructor of the class
     *
     * @param array $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;

        parent::__construct('Payload is not valid.');
    }

    /**
     * {@inheritDoc}
     */
    public function getErrors() : array
    {
        return $this->errors;
    }
}
