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

class AbstractConsumerTest extends \PHPUnit_Framework_TestCase
{
    public function testConsumer()
    {
        $consumer = $this->getMockForAbstractClass('\Euskadi31\Kafka\AbstractConsumer');

        $consumer->addTopic('test', [0, 1]);

        $this->assertEquals($consumer->getTopics(), [
            'test' => [0, 1]
        ]);
    }
}
