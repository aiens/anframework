<?php
class Router{
	public static $route_config = array();
	
	public static function init() {
		if(!get_magic_quotes_gpc()) {
			$_POST = new_addslashes($_POST);
			$_GET = new_addslashes($_GET);
			$_REQUEST = new_addslashes($_REQUEST);
			$_COOKIE = new_addslashes($_COOKIE);			
		}
		self::$route_config = Anframework::load_config('route');		
	}
	
	public static function get(){
		$url = str_replace($_SERVER['SCRIPT_NAME'],'',$_SERVER['REQUEST_URI']);			
		$query_pos = strpos($url, '?');	
		if($query_pos > 0){
			$tmp_url = substr($url, 0, $query_pos);	
		}else{
			$tmp_url = $url;
		}
		$url_array = explode('/', trim($tmp_url,'/'));
		$_controller = addslashes(array_shift($url_array));
		$_action = addslashes(array_shift($url_array));
		if($_controller) self::$route_config['controller'] = $_controller;
		if($_action) self::$route_config['action'] = $_action;
		return self::$route_config;		
	}
	
	public static function safe_deal($str){
		return str_replace(array('/', '.'), '', $str);
	}
}