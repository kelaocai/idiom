<?php
define("APP_PATH",dirname(__FILE__));
define("SP_PATH",dirname(__FILE__).'/SpeedPHP');
require (APP_PATH . "/include/config.php");
$spConfig = array(
 'include_path' => array(
                APP_PATH . '/include') // 用户程序扩展类载入路径
);
require(SP_PATH."/SpeedPHP.php");
spRun();