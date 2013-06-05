<?php
/*
 *
 *@Description 系统运行入口文件
 *@Copyright (C) 2010 - 2013 http://www.anframework.com All rights reserved
 *@License http://www.gnu.org/licenses/gpl-2.0.html
 *@Name index.php
 *@Author Initial: Muke <aiens@woji.net>
 *@Since 2010/05/01
 *@Version 2.0.0
 *
 */
 
define('ROOT_PATH', str_replace("\\", '/', dirname(__FILE__))); //定义框架路径
define('ANF_PATH', str_replace("\\", '/', dirname(__FILE__)).'/framework/'); //定义框架路径
define('APP_PATH', str_replace("\\", '/', dirname(__FILE__)).'/application/'); //定义应用路径
require_once(ANF_PATH.'startup.php'); //加载框架启动器
Anframework::run(); //框架运行