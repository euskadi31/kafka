<?php
/*
 * This file is part of the Kafka.
 *
 * (c) Axel Etcheverry <axel@etcheverry.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Euskadi31\Kafka;

use RdKafka;

/**
 * Kafka Consumer Interface
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
interface ConsumerInterface
{
    const STATUS_CONTINUE       = 1<<0;
    const STATUS_STOP           = 1<<1;
    const STATUS_STORE_OFFSET   = 1<<2;

    /**
     * Configure this consumer
     *
     * @return void
     */
    public function configure();

    /**
     * Add topic and partitions
     *
     * @param string $name       The topic name
     * @param array  $partitions The partitions for the topic
     * @return ConsumerInterface
     */
    public function addTopic($name, array $partitions);

    /**
     * Get topic name and partitions
     *
     * @return array
     */
    public function getTopics();

    /**
     * Get topic config
     *
     * @return RdKafka\TopicConf
     */
    public function getTopicConfig();

    /**
     * Get offset
     *
     * @return integer
     */
    public function getOffset();

    /**
     * Receive error
     *
     * @param  RdKafka\Message $message [description]
     * @return integer
     */
    public function error(RdKafka\Message $message);

    /**
     * Consume message
     *
     * @param  RdKafka\Message $message [description]
     * @return integer
     */
    public function consume(RdKafka\Message $message);
}
