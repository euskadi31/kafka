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
 * Kafka Consumer
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
class Consumer
{
    /**
     * @var RdKafka\Consumer
     */
    protected $kafka;

    /**
     * @var ConsumerInterface
     */
    protected $consumer;

    /**
     * @param RdKafka\Consumer  $kafka
     * @param ConsumerInterface $consumer
     */
    public function __construct(RdKafka\Consumer $kafka, ConsumerInterface $consumer)
    {
        $this->kafka    = $kafka;
        $this->consumer = $consumer;
    }

    /**
     * Run consumer
     *
     * @return void
     */
    public function run()
    {
        $queue = $this->kafka->newQueue();

        $topic = $this->kafka->newTopic(
            $this->consumer->getTopic(),
            $this->consumer->getTopicConfig()
        );

        foreach ($this->consumer->getPartitions() as $partition) {
            $topic->consumeQueueStart(
                $partition,
                $this->consumer->getOffset(),
                $queue
            );
        }

        while (true) {
            $message = $queue->consume($this->consumer->getTimeout());

            if (empty($message)) {
                continue;
            }

            if ($message->err) {
                $status = $this->consumer->error($message);
            } else {
                $status = $this->consumer->consume($message);
            }

            if ($status & ConsumerInterface::STATUS_STORE_OFFSET) {
                $topic->offsetStore($message->partition, $message->offset);
            }

            if ($status & ConsumerInterface::STATUS_STOP) {
                break;
            }
        }
    }
}
