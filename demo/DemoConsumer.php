<?php
/*
 * This file is part of the Kafka.
 *
 * (c) Axel Etcheverry <axel@etcheverry.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acme;

use RdKafka;
use Euskadi31\Kafka\ConsumerInterface;

/**
 * Demo Consumer
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
class DemoConsumer implements ConsumerInterface
{
    /**
     * {@inheritDoc}
     */
    public function getTopic()
    {
        return 'test';
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
    public function getPartitions()
    {
        return [0, 1];
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
    public function getTimeout()
    {
        return 1000;
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
