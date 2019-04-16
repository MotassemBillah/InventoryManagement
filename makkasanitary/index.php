<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/protected/config/functions.php');
defined('YII_DEBUG') or define('YII_DEBUG', true);

$yii = dirname(__FILE__) . '/../../framework/yii.php';

// change the following paths if necessary
$config = dirname(__FILE__) . '/protected/config/main.php';

// Make sure runtime and assets are properly chmod
if (!is_writeable(dirname(__FILE__) . '/protected/runtime')) {
    die('Please chmod 0777 ' . dirname(__FILE__) . '/protected/runtime');
}

require_once($yii);
Yii::createWebApplication($config);
Yii::app()->run();
