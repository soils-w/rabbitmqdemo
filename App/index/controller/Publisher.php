<?php
namespace app\index\controller;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class Publisher
{
    public $config = [
        'host'     => '127.0.0.1',
        'port'     => '5672',
        'user'     => 'root',
        'password' => 'admin',
        'vhost'    => '/'
    ];

    /*
     * Fanout
     * 转发消息到所有绑定队列（最快，广播），不处理路由键。
     * 你只需要简单的将队列绑定到交换机上。一个发送到交换机的消息都会被转发到与该交换机绑定的所有队列上。
     * 很像子网广播，每台子网内的主机都获得了一份复制的消息。Fanout交换机转发消息是最快的。
     */
    public function fanout_publisher()
    {
        $exchange = 'fanout_example_exchange2';
        $queue_name = 'fanout_example_queue2';

        $connection = new AMQPStreamConnection($this->config['host'], $this->config['port'], $this->config['user'], $this->config['password'], $this->config['vhost']);
        $channel = $connection->channel();
        /*
            name: $exchange
            type: fanout
            passive: false // don't check is an exchange with the same name exists
            durable: false // the exchange won't survive server restarts
            auto_delete: true //the exchange will be deleted once the channel is closed.
        */
        $channel->exchange_declare($exchange, AMQPExchangeType::FANOUT, false, false, true);//if exchange doesn't exist, create it
        $channel->queue_declare($queue_name, false, true, false, false);//if it doesn't exist, create it
        $channel->queue_bind($queue_name, $exchange);//交换机和队列绑定
        //一次生产20个
        for($i=0;$i<20;$i++) {
            $data = array(
                'name' => 'li'.$i,
                'qq' => rand(100000,99999999999)
            );
            $messageBody = json_encode($data);
            $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
            $channel->basic_publish($message, $exchange);
        }

        $channel->close();
        $connection->close();
    }

    /**
     * direct
     * 转发消息到routingKey指定队列（完全匹配，单播）,处理路由键。
     * 需要将一个队列绑定到交换机上，要求该消息与一个特定的路由键完全匹配。
     * 这是一个完整的匹配。如果一个队列绑定到该交换机上要求路由键 “test”，则只有被标记为“test”的消息才被转发，不会转发test.aaa，也不会转发dog.123，只会转发test。
     */
    public function direct_publisher()
    {
        $exchange = 'direct_example_exchange';
        $queue_name = 'example_queue';
        $queue_name2 = 'example_queue2';
        $routingkey1 = 'cc1';
        $routingkey2 = 'aa2';

        $connection = new AMQPStreamConnection($this->config['host'], $this->config['port'], $this->config['user'], $this->config['password'], $this->config['vhost']);
        $channel = $connection->channel();
        $channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, false, true);//if exchange doesn't exist, create it
        $channel->queue_declare($queue_name, false, true, false, false);//if it doesn't exist, create it
        $channel->queue_declare($queue_name2, false, true, false, false);//if it doesn't exist, create it
        $channel->queue_bind($queue_name, $exchange,$routingkey1);//交换机和队列绑定,队列1 指定routing key 为cc1
        $channel->queue_bind($queue_name2, $exchange,$routingkey2);//交换机和队列绑定,队列2 指定routing key 为aa2
        //一次生产20个
        for($i=0;$i<20;$i++) {
            $data = array(
                'name' => 'li'.$i,
                'qq' => rand(100000,99999999999)
            );
            $messageBody = json_encode($data);
            $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
            $channel->basic_publish($message, $exchange,$routingkey1);//推送的时候指定routkey为cc1 看测试结果
        }

        $channel->close();
        $connection->close();
    }

    /**
     * TOPIC
     * 按规则转发消息（最灵活，组播）
     * 将路由键和某模式进行匹配。
     * 此时队列需要绑定要一个模式上。符号“#”匹配一个或多个词，符号 “*”匹配不多不少一个词。因此“audit.#”能够匹配到“audit.irs.corporate”，但是“audit.*” 只会匹配到“audit.irs”。
     */
    public function topic_publisher()
    {
        $exchange = 'topic_example_exchange';
        $queue_name = 'example_queue';
        $queue_name2 = 'example_queue2';
        $queue_name3 = 'example_queue3';
        $queue_name4 = 'example_queue4';
        $queue_name5 = 'example_queue5';
        $routingkey1 = 'cc.#';
        $routingkey2 = 'cc.*';
        $routingkey3 = 'cc.1.2';
        $routingkey4 = 'cc.1';

        $connection = new AMQPStreamConnection($this->config['host'], $this->config['port'], $this->config['user'], $this->config['password'], $this->config['vhost']);
        $channel = $connection->channel();
        $channel->exchange_declare($exchange, AMQPExchangeType::TOPIC, false, false, true);//if exchange doesn't exist, create it
        $channel->queue_declare($queue_name, false, true, false, false);//if it doesn't exist, create it
        $channel->queue_declare($queue_name2, false, true, false, false);//if it doesn't exist, create it
        $channel->queue_declare($queue_name3, false, true, false, false);//if it doesn't exist, create it
        $channel->queue_declare($queue_name4, false, true, false, false);//if it doesn't exist, create it
        $channel->queue_declare($queue_name5, false, true, false, false);//if it doesn't exist, create it
        $channel->queue_bind($queue_name, $exchange,$routingkey1);//交换机和队列绑定,队列1 指定bindkey 为$routingkey1
        $channel->queue_bind($queue_name2, $exchange,$routingkey2);//交换机和队列绑定,队列2 指定bindkey key 为$routingkey2
        $channel->queue_bind($queue_name3, $exchange,$routingkey3);//交换机和队列绑定,队列3 指定bindkey key 为$routingkey3
        $channel->queue_bind($queue_name4, $exchange,$routingkey4);//交换机和队列绑定,队列4 指定bindkey key 为$routingkey4
        //一次生产20个
        for($i=0;$i<20;$i++) {
            $data = array(
                'name' => 'li'.$i,
                'qq' => rand(100000,99999999999)
            );
            $messageBody = json_encode($data);
            $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
//            $channel->basic_publish($message, $exchange,'cc.1.2');//推送的时候指定routkey为cc.1.2 ,结果$routingkey为cc.# 的和cc.1.2的都接受到了消息
            $channel->basic_publish($message, $exchange,'cc.1');//推送的时候指定routkey为cc.1 ,结果$routingkey为cc.# 的和cc.1和cc.*de 的都接受到了消息
        }

        $channel->close();
        $connection->close();
    }

}


?>