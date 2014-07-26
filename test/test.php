<?php

require __DIR__ . '/../vendor/autoload.php';

$redis = new \Simplon\Redis\Redis('172.17.0.3', 0);
$redisManger = new \Simplon\Redis\RedisManager($redis);

$resp = $redisManger->getHashCommandsInstance()->setKeyValue('user_1', 'read', 1);
$resp = $redisManger->getHashCommandsInstance()->setKeyValue('user_1', 'load', 1);
$resp = $redisManger->getHashCommandsInstance()->deleteKey('user_1', 'read');

var_dump($resp);