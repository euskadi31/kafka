<?php

$rk = new RdKafka\Producer();
$rk->setLogLevel(LOG_DEBUG);
$rk->addBrokers('127.0.0.1');

$topic = $rk->newTopic('test');

for ($i = 0; $i <= 100000; $i++) {
    $topic->produce(($i % 2), 0, sprintf('message: %d', $i));
}

