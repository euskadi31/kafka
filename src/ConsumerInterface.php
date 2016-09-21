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
     * Get topic name
     *
     * @return string
     */
    public function getTopic();

    /**
     * Get topic config
     *
     * @return RdKafka\TopicConf
     */
    public function getTopicConfig();

    /**
     * Get partitions of topic
     *
     * @return array
     */
    public function getPartitions();

    /**
     * Get offset
     *
     * @return integer
     */
    public function getOffset();

    /**
     * Get timeout (ms) for consuming topic
     *
     * @return integer
     */
    public function getTimeout();

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
