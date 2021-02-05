<?php
// +----------------------------------------------------------------------
// | Author: lq
// +----------------------------------------------------------------------
// [ 应用入口文件 ]

use longq\App;

if (version_compare(PHP_VERSION, '7.1.0', '<'))
	die('require PHP > 7.1.0 !');
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../App/common.php';
$app = new App();
$app->run();