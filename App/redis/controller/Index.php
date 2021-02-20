<?php
namespace app\redis\controller;

use extend\redis\Redis;

class Index
{

    public function publisher()
    {
        $data = [rand(0,10),rand(0,10),rand(9,99),rand(0,10),rand(8,12)]; //这里可以是get或post请求过来的数据
        $data = json_encode($data);
        $redis = new Redis();
        $in = $redis->lpush('queue',$data);
//        $in = $redis->lpush('queueb',$data);
        if($in) {
            echo "入队成功";
        }
    }
    public function customer()
    {
        //正常情况下 有定时脚本 定时获取消息去消费
        $redis = new Redis();
        $value = $redis->lpop('queue');
        $value = json_decode($value,true);
        print_r($value);
        print_r($redis->lrange('queue',0,-1));
    }
    //阻塞类型的消费
    public function customer_zs()
    {
        //正常情况下 有定时脚本 定时获取消息去消费
        $redis = new Redis();
        $value = $redis->blpop(['a','c','queue','d','queueb'],10);
        print_r($value);
        print_r($redis->lrange('queue',0,-1));
        print_r($redis->lrange('queueb',0,-1));
    }

}
?>