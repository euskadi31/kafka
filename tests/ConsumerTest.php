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

class ConsumerTest extends \PHPUnit_Framework_TestCase
{
    public function testConsumer()
    {
        $message1Mock = $this->getMockBuilder('\RdKafka\Message')
            ->getMock();

        $message1Mock->payload = 'Foo';

        $message2Mock = $this->getMockBuilder('\RdKafka\Message')
            ->getMock();

        $message2Mock->err = 'Test error';

        $topicConfigMock = $this->getMockBuilder('\RdKafka\TopicConf')
            ->getMock();

        $consumerMock = $this->getMockBuilder('\Euskadi31\Kafka\ConsumerInterface')
            ->getMock();

        $consumerMock->expects($this->once())
            ->method('getTopic')
            ->will($this->returnValue('test'));

        $consumerMock->expects($this->once())
            ->method('getTopicConfig')
            ->will($this->returnValue($topicConfigMock));

        $consumerMock->expects($this->once())
            ->method('getPartitions')
            ->will($this->returnValue([0, 1]));

        $consumerMock->expects($this->exactly(2))
            ->method('getOffset')
            ->will($this->returnValue(RD_KAFKA_OFFSET_BEGINNING));

        $consumerMock->expects($this->exactly(3))
            ->method('getTimeout')
            ->will($this->returnValue(1000));

        $consumerMock->expects($this->once())
            ->method('consume')
            ->with($this->equalTo($message1Mock))
            ->will($this->returnValue(ConsumerInterface::STATUS_CONTINUE));

        $consumerMock->expects($this->once())
            ->method('error')
            ->with($this->equalTo($message2Mock))
            ->will($this->returnValue(ConsumerInterface::STATUS_STOP));

        $queueMock = $this->getMockBuilder('\RdKafka\Queue')
            ->disableOriginalConstructor()
            ->getMock();

        $queueMock->expects($this->at(0))
            ->method('consume')
            ->with($this->equalTo(1000))
            ->will($this->returnValue(null));

        $queueMock->expects($this->at(1))
            ->method('consume')
            ->with($this->equalTo(1000))
            ->will($this->returnValue($message1Mock));

        $queueMock->expects($this->at(2))
            ->method('consume')
            ->with($this->equalTo(1000))
            ->will($this->returnValue($message2Mock));

        $topicMock = $this->getMockBuilder('\RdKafka\ConsumerTopic')
            ->disableOriginalConstructor()
            ->getMock();

        $topicMock->expects($this->at(0))
            ->method('consumeQueueStart')
            ->with(
                $this->equalTo(0),
                $this->equalTo(RD_KAFKA_OFFSET_BEGINNING),
                $this->equalTo($queueMock)
            );

        $topicMock->expects($this->at(1))
            ->method('consumeQueueStart')
            ->with(
                $this->equalTo(1),
                $this->equalTo(RD_KAFKA_OFFSET_BEGINNING),
                $this->equalTo($queueMock)
            );

        $kafkaMock = $this->getMockBuilder('RdKafka\Consumer')
            ->getMock();

        $kafkaMock->expects($this->once())
            ->method('newQueue')
            ->will($this->returnValue($queueMock));

        $kafkaMock->expects($this->once())
            ->method('newTopic')
            ->with($this->equalTo('test'))
            ->will($this->returnValue($topicMock));

        $consumer = new Consumer($kafkaMock, $consumerMock);

        $consumer->run();
    }

    public function testConsumerWithStoreOffset()
    {
        $message1Mock = $this->getMockBuilder('\RdKafka\Message')
            ->getMock();

        $message1Mock->payload = 'Foo';

        $message2Mock = $this->getMockBuilder('\RdKafka\Message')
            ->getMock();

        $message2Mock->err = 'Test error';

        $topicConfigMock = $this->getMockBuilder('\RdKafka\TopicConf')
            ->getMock();

        $consumerMock = $this->getMockBuilder('\Euskadi31\Kafka\ConsumerInterface')
            ->getMock();

        $consumerMock->expects($this->once())
            ->method('getTopic')
            ->will($this->returnValue('test'));

        $consumerMock->expects($this->once())
            ->method('getTopicConfig')
            ->will($this->returnValue($topicConfigMock));

        $consumerMock->expects($this->once())
            ->method('getPartitions')
            ->will($this->returnValue([0, 1]));

        $consumerMock->expects($this->exactly(2))
            ->method('getOffset')
            ->will($this->returnValue(RD_KAFKA_OFFSET_STORED));

        $consumerMock->expects($this->exactly(2))
            ->method('getTimeout')
            ->will($this->returnValue(1000));

        $consumerMock->expects($this->once())
            ->method('consume')
            ->with($this->equalTo($message1Mock))
            ->will($this->returnValue(ConsumerInterface::STATUS_CONTINUE + ConsumerInterface::STATUS_STORE_OFFSET));

        $consumerMock->expects($this->once())
            ->method('error')
            ->with($this->equalTo($message2Mock))
            ->will($this->returnValue(ConsumerInterface::STATUS_STOP));

        $queueMock = $this->getMockBuilder('\RdKafka\Queue')
            ->disableOriginalConstructor()
            ->getMock();

        $queueMock->expects($this->at(0))
            ->method('consume')
            ->with($this->equalTo(1000))
            ->will($this->returnValue($message1Mock));

        $queueMock->expects($this->at(1))
            ->method('consume')
            ->with($this->equalTo(1000))
            ->will($this->returnValue($message2Mock));

        $topicMock = $this->getMockBuilder('\RdKafka\ConsumerTopic')
            ->disableOriginalConstructor()
            ->getMock();

        $topicMock->expects($this->at(0))
            ->method('consumeQueueStart')
            ->with(
                $this->equalTo(0),
                $this->equalTo(RD_KAFKA_OFFSET_STORED),
                $this->equalTo($queueMock)
            );

        $topicMock->expects($this->at(1))
            ->method('consumeQueueStart')
            ->with(
                $this->equalTo(1),
                $this->equalTo(RD_KAFKA_OFFSET_STORED),
                $this->equalTo($queueMock)
            );

        $kafkaMock = $this->getMockBuilder('RdKafka\Consumer')
            ->getMock();

        $kafkaMock->expects($this->once())
            ->method('newQueue')
            ->will($this->returnValue($queueMock));

        $kafkaMock->expects($this->once())
            ->method('newTopic')
            ->with($this->equalTo('test'))
            ->will($this->returnValue($topicMock));

        $consumer = new Consumer($kafkaMock, $consumerMock);

        $consumer->run();
    }
}
