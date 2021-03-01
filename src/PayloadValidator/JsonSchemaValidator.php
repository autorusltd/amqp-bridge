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
use Arus\AMQP\Bridge\PayloadDecoder\JsonDecoder;
use Arus\AMQP\Bridge\PayloadValidator\Exception\InvalidPayloadException;
use Arus\AMQP\Bridge\MessageInterface;
use JsonSchema\Validator;
use RuntimeException;
use stdClass;

/**
 * Import functions
 */
use function is_file;
use function is_readable;
use function realpath;

/**
 * JsonSchemaValidator
 *
 * @link https://github.com/justinrainbow/json-schema
 */
final class JsonSchemaValidator implements PayloadValidatorInterface
{

    /**
     * @var string
     */
    private $jsonSchemaFile;

    /**
     * Constructor of the class
     *
     * @param string $jsonSchemaFile
     *
     * @throws RuntimeException
     *         If the given file isn't found or isn't readable...
     */
    public function __construct(string $jsonSchemaFile)
    {
        if (!is_file($jsonSchemaFile)) {
            throw new RuntimeException('Unable to find the JSON schema <' . $jsonSchemaFile . '>.');
        }

        if (!is_readable($jsonSchemaFile)) {
            throw new RuntimeException('Unable to read the JSON schema <' . $jsonSchemaFile . '>.');
        }

        $this->jsonSchemaFile = realpath($jsonSchemaFile);
    }

    /**
     * {@inheritDoc}
     */
    public function validate(MessageInterface $message) : void
    {
        $data = (new JsonDecoder)->decode($message);

        $jsonSchema = new stdClass();
        $jsonSchema->{'$ref'} = 'file://' . $this->jsonSchemaFile;

        $validator = new Validator();
        $validator->validate($data, $jsonSchema);

        if (!$validator->isValid()) {
            throw new InvalidPayloadException($validator->getErrors());
        }
    }
}
