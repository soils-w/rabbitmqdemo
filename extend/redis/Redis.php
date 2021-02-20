<?php
namespace extend\redis;

use Predis\Client;


/**

 * \Redis

 */

class Redis

{

    protected static  $config = [
       'host' => '127.0.0.1',
       'port' => 6379,
       'password' => 'foobared'
   ];

    protected static $redis;

    public static function init()

    {

        self::$redis = new Client(self::$config);

    }

    public static function set($key,$value,$time=null,$unit=null)

    {

        self::init();

        if ($time) {

            switch ($unit) {

                case 'h':

                    $time *= 3600;

                    break;

                case 'm':

                    $time *= 60;

                    break;

                case 's':

                case 'ms':

                    break;

                default:

                    throw new InvalidArgumentException('单位只能是 h m s ms');

                    break;

            }

            if ($unit=='ms') {

                self::_psetex($key,$value,$time);

            } else {

                self::_setex($key,$value,$time);

            }

        } else {

            self::$redis->set($key,$value);

        }

    }

    public static function get($key)

    {

        self::init();

        return self::$redis->get($key);

    }

    public static function delete($key)

    {

        self::init();

        return self::$redis->del($key);

    }

    public static function lpush($key,$value)
    {

        self::init();

        return self::$redis->lpush($key,$value);

    }
    public static function lpop($key)
    {

        self::init();

        return self::$redis->lpop($key);

    }
    public static function blpop($keyarray,$timeout)
    {

        self::init();

        return self::$redis->blpop($keyarray,$timeout);

    }

    public static function lrange($key,$start,$end)
    {

        self::init();

        return self::$redis->lrange($key,$start,$end);

    }

    private static function _setex($key,$value,$time)

    {

        self::$redis->setex($key,$time,$value);

    }

    private static function _psetex($key,$value,$time)

    {

        self::$redis->psetex($key,$time,$value);

    }

}
?>