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
 * Import classes
 */
use Arus\AMQP\Bridge\Annotation;
use Arus\AMQP\Bridge\Exception\UnacknowledgableQueueMessageExceptionInterface;
use Arus\AMQP\Bridge\PayloadDecoder\Exception\UndecodablePayloadExceptionInterface;
use Arus\AMQP\Bridge\PayloadValidator\Exception\InvalidPayloadExceptionInterface;
use Arus\AMQP\Bridge\PayloadValidator\JsonSchemaValidator;
use Arus\AMQP\Bridge\PayloadValidator\PayloadValidatorInterface;
use Doctrine\Common\Annotations\Reader as AnnotationReaderInterface;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Psr\Log\LoggerInterface;
use AMQPEnvelope;
use AMQPQueue;
use Closure;
use ReflectionClass;
use Throwable;

/**
 * Import functions
 */
use function sprintf;

/**
 * Consumer
 */
final class Consumer implements ConsumerInterface
{

    /**
     * @var MessageHandlerInterface
     */
    private $messageHandler;

    /**
     * @var null|LoggerInterface
     */
    private $logger = null;

    /**
     * @var null|PayloadValidatorInterface
     */
    private $payloadValidator = null;

    /**
     * @var null|AnnotationReaderInterface
     */
    private $annotationReader = null;

    /**
     * @var null|Closure
     */
    private $messageReceivedCallback = null;

    /**
     * @var null|Closure
     */
    private $messageHandledCallback = null;

    /**
     * Constructor of the class
     *
     * @param MessageHandlerInterface $messageHandler
     */
    public function __construct(MessageHandlerInterface $messageHandler)
    {
        $this->messageHandler = $messageHandler;
    }

    /**
     * Sets the given logger to the consumer
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger) : void
    {
        $this->logger = $logger;
    }

    /**
     * Sets the given payload validator to the consumer
     *
     * @param PayloadValidatorInterface $payloadValidator
     *
     * @return void
     */
    public function setPayloadValidator(PayloadValidatorInterface $payloadValidator) : void
    {
        $this->payloadValidator = $payloadValidator;
    }

    /**
     * Sets the given annotation reader to the consumer
     *
     * @param AnnotationReaderInterface $annotationReader
     *
     * @return void
     */
    public function setAnnotationReader(AnnotationReaderInterface $annotationReader) : void
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * Sets the given callback that will be called when a queue message is received
     *
     * @param callable $messageReceivedCallback
     *
     * @return void
     */
    public function setMessageReceivedCallback(callable $messageReceivedCallback) : void
    {
        $this->messageReceivedCallback = Closure::fromCallable($messageReceivedCallback);
    }

    /**
     * Sets the given callback that will be called when a queue message is handled
     *
     * @param callable $messageHandledCallback
     *
     * @return void
     */
    public function setMessageHandledCallback(callable $messageHandledCallback) : void
    {
        $this->messageHandledCallback = Closure::fromCallable($messageHandledCallback);
    }

    /**
     * @return void
     */
    public function useJsonSchemaValidator() : void
    {
        if (!($this->annotationReader instanceof AnnotationReaderInterface)) {
            $this->useDefaultAnnotationReader();
        }

        $jsonSchemaReference = $this->annotationReader->getClassAnnotation(
            new ReflectionClass($this->messageHandler),
            Annotation\JsonSchemaReference::class
        );

        if (!($jsonSchemaReference instanceof Annotation\JsonSchemaReference)) {
            return;
        }

        $this->setPayloadValidator(new JsonSchemaValidator($jsonSchemaReference->value));
    }

    /**
     * @return void
     */
    public function useDefaultAnnotationReader() : void
    {
        $annotationReader = new SimpleAnnotationReader();
        $annotationReader->addNamespace(Annotation::class);

        $this->setAnnotationReader($annotationReader);
    }

    /**
     * {@inheritDoc}
     */
    public function process(MessageInterface $message, QueueInterface $queue) : void
    {
        try {
            if ($this->messageReceivedCallback instanceof Closure) {
                ($this->messageReceivedCallback)($message);
            }

            if ($this->payloadValidator instanceof PayloadValidatorInterface) {
                $this->payloadValidator->validate($message);
            }

            try {
                $this->messageHandler->handle($message);
            } finally {
                if ($this->messageHandledCallback instanceof Closure) {
                    ($this->messageHandledCallback)($message);
                }
            }

            $queue->ack($message);
        } catch (UnacknowledgableQueueMessageExceptionInterface $e) {
            $queue->reject($message);

            if ($this->logger instanceof LoggerInterface) {
                $this->logger->error(sprintf(
                    'The queue message <%d> cannot be acknowledged (reject executed). %s',
                    $message->getId(),
                    $e->getMessage()
                ), [
                    'payload' => $message->getPayload(),
                ]);
            }
        } catch (UndecodablePayloadExceptionInterface $e) {
            $queue->reject($message);

            if ($this->logger instanceof LoggerInterface) {
                $this->logger->error(sprintf(
                    'The queue message <%d> contains undecodable payload (reject executed). %s',
                    $message->getId(),
                    $e->getMessage()
                ), [
                    'payload' => $message->getPayload(),
                ]);
            }
        } catch (InvalidPayloadExceptionInterface $e) {
            $queue->reject($message);

            if ($this->logger instanceof LoggerInterface) {
                $this->logger->warning(sprintf(
                    'The queue message <%d> contains invalid payload (reject executed). %s',
                    $message->getId(),
                    $e->getMessage()
                ), [
                    'errors' => $e->getErrors(),
                    'payload' => $message->getPayload(),
                ]);
            }
        } catch (Throwable $e) {
            $queue->requeue($message);

            if ($this->logger instanceof LoggerInterface) {
                $this->logger->error(sprintf(
                    'Unexpected error occurred while handling the queue message <%d> (requeue executed). %s',
                    $message->getId(),
                    $e->getMessage()
                ), [
                    'exception' => $e,
                    'payload' => $message->getPayload(),
                ]);
            }
        }
    }

    /**
     * This method will be called from the `AMQPQueue::consume` method
     *
     * @param AMQPEnvelope $envelope
     * @param AMQPQueue $queue
     *
     * @return void
     */
    public function __invoke(AMQPEnvelope $envelope, AMQPQueue $queue) : void
    {
        $this->process(new Proxy\Message($envelope), new Proxy\Queue($queue));
    }
}
