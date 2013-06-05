<?php
/*
 *
 *@Description 框架公共函数库
 *@Copyright (C) 2010 - 2013 http://www.anframework.com All rights reserved
 *@License http://www.gnu.org/licenses/gpl-2.0.html
 *@Name startup.php
 *@Author Initial: Muke <aiens@woji.net>
 *@Since 2010/05/01
 *@Version 2.0.0
 *
 */


/*
 *
 *错误异常捕获
 *系统默认异常类为Anexception
 *支持设置http头状态码，默认为500（服务器内部错误）
 *$msg 错误信息
 *$type 异常类名称
 *$code http头状态码
 *
 */
function throw_exception($msg, $type = 'Anexception', $code = 500) {
	set_status_header($code);
	if(class_exists($type, false)){
		throw new $type($msg, $code, true);
	}else{
		halt($msg);
	}
}

/*
 *
 *系统终止器
 *系统错误信息输出，同时停止系统运行
 *$error 错误信息
 *
 */
function halt($error){	
	$e = array();
	if(IS_DEBUG){
		if(!is_array($error)){
			$trace = debug_backtrace();
			$e['message'] = $error;
			$e['file'] = $trace[0]['file'];
			$e['class'] = isset($trace[0]['class']) ? $trace[0]['class'] : '';
			$e['function'] = isset($trace[0]['function']) ? $trace[0]['function'] : '';
			$e['line'] = $trace[0]['line'];
			$trace_info = '';
			$time = date('y-m-d H:i:m');
			foreach($trace as $t){
				$trace_info .= '['.$time.'] '.$t['file'].' ('.$t['line'].') ';
				$trace_info .= $t['class'].$t['type'].$t['function'].'(';
				$trace_info .= implode(', ', $t['args']);
				$trace_info .=')<br/>';
			}
			$e['trace'] = $trace_info;
		}else{
			$e = $error;
		}
	}else{
		$e['message'] = is_array($error) ? $error['message'] : $error;
	}
	require_once ANF_PATH.'library/output/error.php';
	exit;
}

/*
 *
 *字符串转义
 *支持数组转义
 *$string 字符串或者数组
 *
 */
function an_addslashes($string){
	if(!is_array($string)){
		return addslashes($string);
	}
	foreach($string as $key => $val){
		$string[$key] = an_addslashes($val);
	}
	return $string;
}

/*
 *
 *模板启动器
 *支持模板缓存
 *$c 控制器名称
 *$a 控制器的方法名称
 *template/控制器名称/方法名称.html
 *
 */
function template($c = 'index', $a = 'init'){
	Anframework::load_file('template', 'class', 1);
	$compiled_tpl_file = APP_PATH.'cache/compiled_template/'.$c.'/'.$a.'.php';
	if(file_exists(APP_PATH.'template/'.$c.'/'.$a.'.html')){
		if(!file_exists($compiled_tpl_file) || (@filemtime(APP_PATH.'template/'.$c.'/'.$a.'.html') > @filemtime($compiled_tpl_file))){
			Template::template_compile($c, $a);
		}
	}else{
		throw_exception('Template file does not exist.'.'(template/'.$c.'/'.$a.'.html)');
	}
	return $compiled_tpl_file;
}

/*
 *
 *CSS/JS加载器
 *$c 控制器名称
 *$a 控制器的方法名称
 *
 */
function set_front_file($c = 'index', $a = 'init', $type = 'css', $cache = 0){
	Anframework::load_file('template', 'class', 1);
	Template::get_front_file($c, $a, $type, $cache);
}


/*
 *
 *HTTP头设置
 *$code 默认为200
 *$text 头信息
 *
 */
function set_status_header($code = 200, $text = ''){
	$code = intval($code);
	$status_arr = array(200 => 'OK',201 => 'Created',202 => 'Accepted',203 => 'Non-Authoritative Information',204 => 'No Content',205 => 'Reset Content',206 => 'Partial Content',

	300 => 'Multiple Choices',301 => 'Moved Permanently',302 => 'Found',304 => 'Not Modified',305 => 'Use Proxy',307 => 'Temporary Redirect',

	400 => 'Bad Request',401 => 'Unauthorized',403 => 'Forbidden',404 => 'Not Found',405 => 'Method Not Allowed',406 => 'Not Acceptable',407 => 'Proxy Authentication Required',408 => 'Request Timeout',409 => 'Conflict',410 => 'Gone',411 => 'Length Required',412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',414 => 'Request-URI Too Long',415 => 'Unsupported Media Type',416 => 'Requested Range Not Satisfiable',417 => 'Expectation Failed',

			500 => 'Internal Server Error',501 => 'Not Implemented',502 => 'Bad Gateway',503 => 'Service Unavailable',504 => 'Gateway Timeout',505 => 'HTTP Version Not Supported' );	

	if(isset($status_arr[$code]) and $text == ''){
		$text = $status_arr[$code];
	}

	$server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;
	if(IS_CGI){
		header("Status: {$code} {$text}", true);
	}elseif($server_protocol == 'HTTP/1.1' or $server_protocol == 'HTTP/1.0'){
		header($server_protocol." {$code} {$text}", true, $code);
	}else{
		header("HTTP/1.1 {$code} {$text}", true, $code);
	}
}

/*
 *
 *获取客户端IP
 *
 */
function get_client_ip(){
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')){
		$ip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')){
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')){
		$ip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')){
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
}

/*
 *
 *获取服务端IP
 *
 */
function get_server_ip() { 
    if (isset($_SERVER)) { 
        if($_SERVER['SERVER_ADDR']) {
            $server_ip = $_SERVER['SERVER_ADDR']; 
        } else { 
            $server_ip = $_SERVER['LOCAL_ADDR']; 
        } 
    } else { 
        $server_ip = getenv('SERVER_ADDR');
    } 
    return $server_ip; 
}

/*
 *
 *字符串加密解密
 *$string 字符串
 *$operation 操作方式 ENCODE/DECODE
 *$key 加密解密密钥
 *$expiry 过期时间
 *
 */
function str_auth($string, $operation = 'ENCODE', $key = '', $expiry = 0){
	$ckey_length = 4;
	$key = md5($key != '' ? $key : Anframework::load_config('system', 'auth_key'));
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}

/*
 *
 *设置COOKIE
 *$var cookie名称
 *$value cookie值
 *$time 有效期
 *
 */
function set_cookie($var, $value = '', $time = 0){
	$time = $time > 0 ? $time : ($value == '' ? SYS_TIME - 3600 : 0);
	$s = $_SERVER['SERVER_PORT'] == '443' ? 1 : 0;
	$var = Anframework::load_config('system','cookie_pre').$var;
	$_COOKIE[$var] = $value;
	if (is_array($value)){
		foreach($value as $k=>$v){
			setcookie($var.'['.$k.']', str_auth($v, 'ENCODE'), $time, Anframework::load_config('system','cookie_path'), Anframework::load_config('system','cookie_domain'), $s);
		}
	}else{
		setcookie($var, str_auth($value, 'ENCODE'), $time, Anframework::load_config('system','cookie_path'), Anframework::load_config('system','cookie_domain'), $s);
	}
}

/*
 *
 *获取COOKIE
 *$var cookie名称
 *
 */	
function get_cookie($var){
	$var = Anframework::load_config('system','cookie_pre').$var;
	return isset($_COOKIE[$var]) ? str_auth($_COOKIE[$var], 'DECODE') : '';
}

/*
 *
 *设置缓存
 *$name 缓存名称
 *$data 缓存内容
 *$file_path 缓存存放路径
 *$timeout 过期时间
 *
 */ 
function set_cache($name, $data, $file_path = '', $timeout = ''){	
	$cache = Cache::get_instance()->get_driver();
	return $cache->set($name, $data, $file_path, $timeout);
}

/*
 *
 *获取缓存
 *$name 缓存名称
 *$file_path 缓存存放路径
 *
 */ 
function get_cache($name, $file_path=''){
	$cache = Cache::get_instance()->get_driver();
	return $cache->get($name, $file_path);
}

/*
 *
 *提示信息器
 *$msg 提示内容
 *$jump_url 跳转url地址
 *$ms 跳转等待时间
 *
 */ 
function show_message($msg, $jump_url = 'goback', $ms = 1250){
	$powered_by = 'Powered by AnFramework Version '.ANF_VERSION.' Release'.ANF_RELEASE;
	include template('common', 'message');
	exit;
}

/*
 *
 *获取当前URL地址
 *
 */ 
function get_url(){
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
	$path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.safe_replace($_SERVER['QUERY_STRING']) : $path_info);
	return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

/*
 *
 *时间差速器
 *计算出2个时间之间的间隔
 *$begin_time 开始时间戳
 *$end_time 结束时间戳
 *@return array 相差天数、小时、分钟、秒数
 *
 */
function time_diff($begin_time, $end_time){ 
     if($begin_time < $end_time){ 
        $starttime = $begin_time; 
        $endtime = $end_time; 
     }else{ 
        $starttime = $end_time; 
        $endtime = $begin_time; 
     } 
     $timediff = $endtime-$starttime; 
     $days = intval($timediff/86400); 
     $remain = $timediff%86400; 
     $hours = intval($remain/3600); 
     $remain = $remain%3600; 
     $mins = intval($remain/60); 
     $secs = $remain%60; 
     $res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs); 
     return $res; 
}

/*
 *
 *字符安全过滤替换
 *用于url地址字符串
 *$string 字符串
 *
 */
function safe_replace($string){
	$string = str_replace('%20','',$string);
	$string = str_replace('%27','',$string);
	$string = str_replace('%2527','',$string);
	$string = str_replace('*','',$string);
	$string = str_replace('"','&quot;',$string);
	$string = str_replace("'",'',$string);
	$string = str_replace('"','',$string);
	$string = str_replace(';','',$string);
	$string = str_replace('<','&lt;',$string);
	$string = str_replace('>','&gt;',$string);
	$string = str_replace("{",'',$string);
	$string = str_replace('}','',$string);
	$string = str_replace('\\','',$string);
	return $string;
}

/*
 *
 *数组转SQL
 *支持 key IN (1,2,3,4)
 *支持 key1 = val1 AND key2 = val2
 *$data 数组
 *$in_column IN()模式
 *
 */
function arr_to_sqls($data, $in_column = false) {
	if($in_column && is_array($data)) {
		$ids = '\''.implode('\',\'', $data).'\'';
		$sql = "$in_column IN ($ids)";
		return $sql;
	} else {
		$front = ' AND ';
		if(is_array($data) && count($data) > 0) {
			$sql = '';
			foreach ($data as $key => $val) {
				$sql .= $sql ? " $front `$key` = '$val' " : " `$key` = '$val' ";
			}
			return $sql;
		} else {
			return $data;
		}
	}
}

function set_config($config,$cfgfile) {
	if(!$config || !$cfgfile) return false;
	$configfile = APP_PATH.'config/'.$cfgfile.'.php';
	if(!is_writable($configfile)) show_message('Please chmod '.$configfile.' to 0777 !');
	$pattern = $replacement = array();
	foreach($config as $k=>$v) {
			$v = trim($v);
			$configs[$k] = $v;
			$pattern[$k] = "/'".$k."'\s*=>\s*([']?)[^']*([']?)(\s*),/is";
        	$replacement[$k] = "'".$k."' => \${1}".$v."\${2}\${3},";							
	}
	$str = file_get_contents($configfile);
	$str = preg_replace($pattern, $replacement, $str);
	return file_put_contents($configfile, $str);		
}


function random_str($lenth = 6) {
	return random($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
}

function random($length, $chars = '0123456789') {
	$hash = '';
	$max = strlen($chars) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}