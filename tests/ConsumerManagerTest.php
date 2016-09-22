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

class ConsumerManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testConsumerManagerEmpty()
    {
        $kafkaMock = $this->getMockBuilder('RdKafka\Consumer')
            ->getMock();

        $kafkaMock->expects($this->once())
            ->method('newQueue');

        $consumerManager = new ConsumerManager($kafkaMock);
        $consumerManager->run();
    }

    public function testConsumerManager()
    {
        $message1Mock = $this->getMockBuilder('\RdKafka\Message')
            ->getMock();

        $message1Mock->topic_name = 'test';
        $message1Mock->partition = 0;
        $message1Mock->err = RD_KAFKA_RESP_ERR_NO_ERROR;
        $message1Mock->payload = 'Foo';

        $message2Mock = $this->getMockBuilder('\RdKafka\Message')
            ->getMock();

        $message2Mock->topic_name = 'test';
        $message2Mock->partition = 1;
        $message2Mock->err = RD_KAFKA_RESP_ERR__PARTITION_EOF;

        $message3Mock = $this->getMockBuilder('\RdKafka\Message')
            ->getMock();

        $message3Mock->topic_name = 'test';
        $message3Mock->partition = 0;
        $message3Mock->err = RD_KAFKA_RESP_ERR__FAIL;

        $message4Mock = $this->getMockBuilder('\RdKafka\Message')
            ->getMock();

        $message4Mock->topic_name = 'test';
        $message4Mock->partition = 0;
        $message4Mock->err = RD_KAFKA_RESP_ERR_BROKER_NOT_AVAILABLE;

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

        $queueMock->expects($this->at(3))
            ->method('consume')
            ->with($this->equalTo(1000))
            ->will($this->returnValue($message3Mock));

        $queueMock->expects($this->at(4))
            ->method('consume')
            ->with($this->equalTo(1000))
            ->will($this->returnValue($message4Mock));

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

        $kafkaMock->expects($this->exactly(2))
            ->method('newTopic')
            ->with($this->equalTo('test'))
            ->will($this->returnValue($topicMock));

        $topicConfigMock = $this->getMockBuilder('\RdKafka\TopicConf')
            ->getMock();

        $consumerMock = $this->getMockBuilder('\Euskadi31\Kafka\ConsumerInterface')
            ->getMock();

        $consumerMock->expects($this->once())
            ->method('configure');

        $consumerMock->expects($this->once())
            ->method('getTopicConfig')
            ->will($this->returnValue($topicConfigMock));

        $consumerMock->expects($this->once())
            ->method('getTopics')
            ->will($this->returnValue([
                'test' => [0, 1]
            ]));

        $consumerMock->expects($this->exactly(2))
            ->method('getOffset')
            ->will($this->returnValue(RD_KAFKA_OFFSET_BEGINNING));

        $consumerMock->expects($this->once())
            ->method('consume')
            ->with($this->equalTo($message1Mock))
            ->will($this->returnValue(ConsumerInterface::STATUS_CONTINUE));

        $consumerMock->expects($this->once())
            ->method('error')
            ->with($this->equalTo($message3Mock))
            ->will($this->returnValue(ConsumerInterface::STATUS_STOP));

        $consumerManager = new ConsumerManager($kafkaMock);
        $consumerManager->addConsumer($consumerMock);
        $consumerManager->run();
    }
}
