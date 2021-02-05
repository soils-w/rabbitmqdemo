<?php
namespace longq;

class App
{
    public $c;
    public $a;
    public function run()
    {
        ini_set("display_errors", "On");
        error_reporting(E_ALL | E_STRICT);
        $c = isset($_GET['c']) ? $_GET['c'] : "Index"; //url提供类名字的变量名
        $a = isset($_GET['a']) ? $_GET['a'] : "Index"; //url提供方法名字的变量名
        $p = isset($_GET['p']) ? $_GET['p'] : "Index"; //url提供 基础路径
        $dir = dirname(__DIR__).'/App/'.$p;
        if (!is_dir($dir)) {
            exit("dir not exits");
        }
        $cdir = $dir.'/controller/';
        if(!file_exists($cdir.$c.'.php')) {
            exit("file not exits");
        }
        try {
            $classname = "\app\\".$p."\controller\\".$c;
            $new = new $classname();
            $new->$a();
        } catch(\Exception $e) {
            echo $e->getMessage();die();
        }
    }

}