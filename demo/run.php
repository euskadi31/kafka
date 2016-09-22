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
use Euskadi31;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/DemoConsumer.php';
require __DIR__ . '/Demo2Consumer.php';

$rk = new RdKafka\Consumer();
$rk->setLogLevel(LOG_DEBUG);
$rk->addBrokers('127.0.0.1');

$consumerManager = new Euskadi31\Kafka\ConsumerManager($rk);
$consumerManager->addConsumer(new DemoConsumer());
$consumerManager->addConsumer(new Demo2Consumer());
$consumerManager->run();
