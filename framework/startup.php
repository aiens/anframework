<?php
/*
 *
 *@Description 框架启动器
 *@Copyright (C) 2010 - 2013 http://www.anframework.com All rights reserved
 *@License http://www.gnu.org/licenses/gpl-2.0.html
 *@Name startup.php
 *@Author Initial: Muke <aiens@woji.net>
 *@Since 2010/05/01
 *@Version 2.0.0
 *
 */
 
define('ANF_VERSION', '2.0.0'); //框架版本
define('ANF_RELEASE', '20130520'); //框架编译日期
!defined('ANF_PATH') && define('ANF_PATH', str_replace("\\", '/',dirname(__FILE__))); //框架目录定义检测
define('SYS_TIME', time()); //系统时间
define('SYS_START_TIME', microtime()); //框架初始时间
define('IN_ANF', true); //框架标识
define('IS_DEBUG', true); //运行模式
define('FIRE_BUG', true); //是否启用firebug调试
define('OUTPUT_ENCODE', true); //输出是否加密
define('SITE_PROTOCOL', isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://'); //主机协议
define('SITE_URL', (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '')); //当前访问的主机名
define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''); //来源
define('IS_CGI', substr(PHP_SAPI, 0, 3) == 'cgi' ? true : false); //PHP是否为CGI模式运行
define('IS_WIN', strstr(PHP_OS, 'WIN') ? true : false); //是否是windows系统
define('IS_CLI', PHP_SAPI == 'cli' ? true : false); //PHP是否为CLI模式运行
define('CACHE_PATH', APP_PATH.'cache/'); //缓存目录

error_reporting(E_ALL & ~E_NOTICE); //错误抑制配置

require_once(ANF_PATH.'library/function/common.func.php'); //加载框架公共函数库
require_once(ANF_PATH.'ANFramework.php'); //加载框架核心
set_exception_handler(array('Anframework','an_exception')); //定义系统错误异常

define('APP_URL', Anframework::load_config('system', 'app_url')); //定义应用URL地址 eg:http://oa.woji.net/
define('WEB_PATH', Anframework::load_config('system', 'web_path'));  //定义应用目录 eg:/
define('JS_PATH', Anframework::load_config('system', 'js_path')); //定义JS目录URL地址
define('CSS_PATH', Anframework::load_config('system', 'css_path')); //定义CSS目录URL地址
define('IMG_PATH', Anframework::load_config('system', 'img_path')); //定义IMAGE目录URL地址
define('IP', get_client_ip()); //获取客户端IP
define('SER_IP', get_server_ip()); //获取服务端IP
define('CHARSET', Anframework::load_config('system', 'charset'));header('Content-type: text/html; charset='.CHARSET); //定义页面编码
function_exists('date_default_timezone_set') && date_default_timezone_set(Anframework::load_config('system','timezone')); //定义系统时区
(Anframework::load_config('system','gzip') && function_exists('ob_gzhandler')) ? ob_start('ob_gzhandler') : ob_start(); //GZIP操作