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
use Euskadi31\Kafka\AbstractConsumer;

/**
 * Demo Consumer
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
class Demo2Consumer extends AbstractConsumer
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->addTopic('test.old', [1]);
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
        echo sprintf(
            '%s: (%s/%d): %s',
            __CLASS__,
            $message->topic_name,
            $message->partition,
            $message->payload
        ) . PHP_EOL;

        return self::STATUS_CONTINUE;
    }
}
