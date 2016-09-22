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

/**
 * Kafka Abstract Consumer
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
abstract class AbstractConsumer implements ConsumerInterface
{
    /**
     * @var array
     */
    protected $topics = [];

    /**
     * {@inheritDoc}
     */
    abstract public function configure();

    /**
     * {@inheritDoc}
     */
    public function addTopic($name, array $partitions)
    {
        $this->topics[$name] = $partitions;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getTopics()
    {
        return $this->topics;
    }
}
