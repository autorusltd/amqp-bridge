# Bridge to AMQP extension for PHP 7.1+ (incl. PHP 8) with support for annotations and Json Schema

[![Build Status](https://circleci.com/gh/autorusltd/amqp-bridge.svg?style=shield)](https://circleci.com/gh/autorusltd/amqp-bridge)
[![Code Coverage](https://scrutinizer-ci.com/g/autorusltd/amqp-bridge/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/autorusltd/amqp-bridge/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/autorusltd/amqp-bridge/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/autorusltd/amqp-bridge/?branch=master)
[![Total Downloads](https://poser.pugx.org/arus/amqp-bridge/downloads?format=flat)](https://packagist.org/packages/arus/amqp-bridge)
[![Latest Stable Version](https://poser.pugx.org/arus/amqp-bridge/v/stable?format=flat)](https://packagist.org/packages/arus/amqp-bridge)
[![License](https://poser.pugx.org/arus/amqp-bridge/license?format=flat)](https://packagist.org/packages/arus/amqp-bridge)

---

## Installation

```bash
composer require 'arus/amqp-bridge'
```

## QuickStart

#### Queue Message Handler

```php
declare(strict_types=1);

namespace App\QueueMessageHandler;

use Arus\AMQP\Bridge\PayloadDecoder\JsonDecoder;
use Arus\AMQP\Bridge\MessageHandlerInterface;
use Arus\AMQP\Bridge\MessageInterface;

use const JSON_OBJECT_AS_ARRAY;

/**
 * @JsonSchemaReference("config/json-schemas/SomeQueueMessage.json")
 */
final class SomeQueueMessageHandler implements MessageHandlerInterface
{

    /**
     * {@inheritDoc}
     */
    public function handle(MessageInterface $message) : void
    {
        $data = (new JsonDecoder)->decode($message, JSON_OBJECT_AS_ARRAY);

        // some code...
    }
}
```

#### Message Queue Consumer

```php
use App\QueueMessageHandler\SomeQueueMessageHandler;
use Arus\AMQP\Bridge\Consumer;

$connection = new AMQPConnection();
$connection->setHost('localhost');
$connection->setPort(5672);
$connection->setVhost('/');
$connection->setLogin('guest');
$connection->setPassword('guest');
$connection->connect();

$channel = new AMQPChannel($connection);
$channel->setPrefetchCount(100);

$queue = new AMQPQueue($channel);
$queue->setName('queue.name');

// init the message queue consumer...
$consumer = new Consumer(new SomeQueueMessageHandler());
// [optional] set a logger based on PSR-3...
$consumer->setLogger($logger);
// [optional] set a custom payload validator...
$consumer->setPayloadValidator($payloadValidator);
// [optional] set a custom annotation reader...
$consumer->setAnnotationReader($annotationReader);
// [optional] use a JSON schema validator for queue messages...
$consumer->useJsonSchemaValidator();
// [optional] set a callback that will be called when a queue message is received...
$consumer->setMessageReceivedCallback(function () {
    // here you can, for example, re-open doctrine entity managers...
});
// [optional] set a callback that will be called when a queue message is handled...
$consumer->setMessageHandledCallback(function () {
    // here you can, for example, clear doctrine entity managers...
});

try {
    $queue->consume($consumer);
} catch (Throwable $e) {
    $connection->disconnect();

    throw $e;
}
```

## Acknowledge, reject and requeue commands

* If a queue message was handled **without errors**, such a message will be **automatically acknowledged**;
* If a queue message contains **undecodable** or **invalid** payload, such a message will be **automatically rejected**;
* If a queue message was handled with **an unexpected error**, such a message will be **automatically requeued**;
* If you need to **reject a queue message in code**, just throw an exception `Arus\AMQP\Bridge\Exception\UnacknowledgableQueueMessageExceptionInterface`.

---

## Test run

```bash
composer test
```

---

## Useful links

* https://github.com/php-amqp/php-amqp
