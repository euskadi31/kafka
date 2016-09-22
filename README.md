# Kafka Framework

[![Build Status](https://img.shields.io/travis/euskadi31/kafka/master.svg)](https://travis-ci.org/euskadi31/kafka)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/27bdfd3f-f66a-4e81-ae93-560c333adc16.svg)](https://insight.sensiolabs.com/projects/27bdfd3f-f66a-4e81-ae93-560c333adc16)
[![Coveralls](https://img.shields.io/coveralls/euskadi31/kafka.svg)](https://coveralls.io/github/euskadi31/kafka)
[![HHVM](https://img.shields.io/hhvm/euskadi31/kafka.svg)](https://travis-ci.org/euskadi31/kafka)
[![Packagist](https://img.shields.io/packagist/v/euskadi31/kafka.svg)](https://packagist.org/packages/euskadi31/kafka)


## Install

Add `euskadi31/kafka` to your `composer.json`:

    % php composer.phar require euskadi31/kafka:~1.0

## Usage

### Configuration

```php
<?php

namespace Acme;

use RdKafka;
use Euskadi31\Kafka\AbstractConsumer;

/**
 * Demo Consumer
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
class DemoConsumer extends AbstractConsumer
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->addTopic('test', [0, 1]);
        $this->addTopic('test.old', [0]);
    }

    /**
     * {@inheritDoc}
     */
    public function getTopicConfig()
    {
        return new RdKafka\TopicConf();
    }

    /**
     * {@inheritDoc}
     */
    public function getOffset()
    {
        return RD_KAFKA_OFFSET_BEGINNING;
    }

    /**
     * {@inheritDoc}
     */
    public function error(RdKafka\Message $message)
    {
        echo $message->errstr() . PHP_EOL;

        return self::STATUS_STOP;
    }

    /**
     * {@inheritDoc}
     */
    public function consume(RdKafka\Message $message)
    {
        echo $message->payload . PHP_EOL;

        return self::STATUS_CONTINUE;
    }
}


```

```php
<?php

$rk = new RdKafka\Consumer();
$rk->setLogLevel(LOG_DEBUG);
$rk->addBrokers("10.0.0.1,10.0.0.2");

$consumer = new Euskadi31\Kafka\Consumer($rk);
$consumer->addConsumer(new Acme\DemoConsumer());
$consumer->run();

```

## License

Kafka is licensed under [the MIT license](LICENSE.md).
