<?php
namespace app\index\controller;

require __DIR__ . '/../../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

set_time_limit(300);

$config = [
    'host'     => '127.0.0.1',
    'port'     => '5672',
    'user'     => 'root',
    'password' => 'admin',
    'vhost'    => '/'
];
$queue_name = 'fanout_example_queue';

$connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password'], $config['vhost']);
$channel = $connection->channel();

$message = $channel->basic_get($queue_name);
$channel->basic_ack($message->delivery_info['delivery_tag']);//手动确认，获取成功后删除队列中的消息
echo $message->body;

$channel->close();
$connection->close();