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
use SplObjectStorage;
use ArrayObject;

/**
 * Kafka Consumer Manager
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
class ConsumerManager
{
    /**
     * @var RdKafka\Consumer
     */
    protected $kafka;

    /**
     * @var SplObjectStorage
     */
    protected $consumers;

    /**
     * Nomber of consumer running
     *
     * @var integer
     */
    protected $running;

    /**
     * @var array
     */
    protected $topics;

    /**
     * @var integer
     */
    protected $timeout;

    /**
     * @param RdKafka\Consumer $kafka
     * @param integer          $timeout
     */
    public function __construct(RdKafka\Consumer $kafka, $timeout = 1000)
    {
        $this->kafka        = $kafka;
        $this->consumers    = new SplObjectStorage();
        $this->timeout      = $timeout;
        $this->topics       = [];
    }

    /**
     * Add consumer
     *
     * @param ConsumerInterface $consumer
     * @return ConsumerManager
     */
    public function addConsumer(ConsumerInterface $consumer)
    {
        if (!$this->consumers->contains($consumer)) {
            $consumer->configure();

            $this->consumers->attach($consumer);
        }

        return $this;
    }

    /**
     * Register consumer to queue
     *
     * @param  RdKafka\Queue $queue
     * @return void
     */
    protected function registerConsumer(RdKafka\Queue $queue, ConsumerInterface $consumer)
    {
        $config = $consumer->getTopicConfig();

        foreach ($consumer->getTopics() as $name => $partitions) {
            foreach ($partitions as $partition) {
                $topic = $this->kafka->newTopic($name, $config);

                $topic->consumeQueueStart(
                    (int) $partition,
                    $consumer->getOffset(),
                    $queue
                );

                if (!isset($this->topics[$name])) {
                    $this->topics[$name] = [];
                }

                if (!isset($this->topics[$name][$partition])) {
                    $this->topics[$name][$partition] = [];
                }

                $this->topics[$name][$partition][] = new ArrayObject([
                    'topic'     => $topic,
                    'consumer'  => $consumer,
                    'running'   => true
                ]);

                $this->running++;
            }
        }
    }

    /**
     * Get resolve topic and consumer for topic name and partition
     *
     * @param  string  $name      The topic name
     * @param  integer $partition The partition
     * @return array
     */
    public function resolve($name, $partition)
    {
        if (isset($this->topics[$name][$partition])) {
            return $this->topics[$name][$partition];
        }

        return [];
    }

    /**
     * Run consumer
     *
     * @return void
     */
    public function run()
    {
        $queue = $this->kafka->newQueue();

        foreach ($this->consumers as $consumer) {
            $this->registerConsumer($queue, $consumer);
        }

        if (empty($this->topics)) {
            return;
        }

        while (true) {
            $message = $queue->consume($this->timeout);

            if (empty($message)) {
                continue;
            }

            if ($message->err == RD_KAFKA_RESP_ERR_BROKER_NOT_AVAILABLE) {
                break;
            }

            // @codingStandardsIgnoreStart
            $topic = $message->topic_name;
            // @codingStandardsIgnoreEnd

            foreach ($this->resolve($topic, $message->partition) as $item) {
                if (!$item['running']) {
                    continue;
                }

                switch ($message->err) {
                    case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                        continue;
                        break;
                    case RD_KAFKA_RESP_ERR_NO_ERROR:
                        $status = $item['consumer']->consume($message);
                        break;
                    default:
                        $status = $item['consumer']->error($message);
                        break;
                }

                if ($status & ConsumerInterface::STATUS_STORE_OFFSET) {
                    $item['topic']->offsetStore($message->partition, $message->offset);
                }

                if ($status & ConsumerInterface::STATUS_STOP) {
                    $item['topic']->consumeStop($message->partition);
                    $item['running'] = false;
                    $this->running--;
                }
            }

            if ($this->running == 0) {
                break;
            }
        }
    }
}
