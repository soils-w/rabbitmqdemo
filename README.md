# rabbitmq 入门demo，手把手教学

> 运行环境要求PHP7+。

## 功能

* 生产包括fanout示例 direct示例 topic示例
* 消费包括监听模式和get模式的示例
* 原生php

## 安装

下载下来代码之后，运行composer
~~~
composer update
~~~

#### 消费者可以直接进入到程序所在目录命令运行
~~~
php consumer.php
~~~

生产者需要配置网站目录，访问方式：
~~~
网站路径/index.php/index/publisher/topic_publisher
~~~

关于rabbitmq的安装可以参考地址：[rabbitmq-1 window安装rabbitmq&&wamp安装amqp扩展](https://blog.csdn.net/qq_38475911/article/details/112993749?spm=1001.2014.3001.5502)
