<?php
class Anframework{
	public static function run(){
		self::init();
		Application::init();
	}
	
	private static function init(){
		self::load_file('anexception', 'class'); //异常控制		
		self::load_file('application', 'class'); //应用
		self::load_file('router', 'class'); //路由器
		self::load_file('model', 'class'); //模型
		self::load_file('controller', 'class'); //控制器
		self::load_file('database', 'class'); //数据库
		self::load_file('session', 'class'); //会话
		self::load_file('cache', 'class'); //缓存
		Router::init();		
	}	
	
	public static function an_exception($e){
		if(FIRE_BUG){
			self::load_file('firephp', 'class'); //firePHP
			fb($e);
		}
        halt($e->__toString());
    }	
	
	public static function load_file($name, $type = 'class', $initialize = 0, $file_path = null){
		switch($type){
			case 'class':
				return self::load_class($name, $initialize, $file_path);
				break;	
			case 'function':
			    return self::load_function($name, $file_path);
				break;
			case 'common':
			    return self::_load_file($name, $file_path);
				break;
			case 'model':
				return self::load_model($name, $initialize, $file_path);
				break;		
		}				
	}
	
	private static function _load_file($file_name, $file_path){
		static $load_files = array();
		if(empty($file_path)) $file_path = ANF_PATH.'library/common';
		$file_path .= '/'.$file_name.'.php';
		$key = md5($file_path);
		if(isset($load_files[$key])) return true;
		if(file_exists($file_path)){
			include_once $file_path;
		}else{
			$load_files[$key] = false;
			throw_exception('Unable to load the file '.$file_path.' , file is not exist.');
			return false;
		}
		$load_files[$key] = true;
		return true;
	}
	
	private static function load_function($function_name, $file_path = ''){
		static $funcs = array();
		if(empty($file_path)) $file_path = 'library/function';
		$file_path .= '/'.$function_name.'.func.php';
		$key = md5($file_path);
		if(isset($funcs[$key])) return true;
		
		if(file_exists(ANF_PATH.$file_path)){
			include_once ANF_PATH.$file_path;
		}else{
			$funcs[$key] = false;
			throw_exception('Unable to load the file '.$file_path.' , file is not exist.');
			return false;
		}
		$funcs[$key] = true;
		return true;
	}
	
	private static function load_class($class_name, $initialize = 0, $file_path = '') {
		static $classes = array();
		if(empty($file_path)) $file_path = 'library/class';

		$key = md5($file_path.$class_name);
		if(isset($classes[$key])){
			if(!empty($classes[$key])){
				return $classes[$key];
			}else{
				return true;
			}
		}

		if(file_exists(ANF_PATH.$file_path.'/'.$class_name.'.class.php')){						
			include_once ANF_PATH.$file_path.'/'.$class_name.'.class.php';			
			if(strpos($class_name, '/') !== false){
				$class_name = explode('/', $class_name);
				$class_name = $class_name[1];
			}
			$class_name = strtolower($class_name);
			$name = ucfirst($class_name);
			if(class_exists($name)){
				if($initialize){
					$classes[$key] = new $name;
				}else{
					$classes[$key] = true;
				}
				return $classes[$key];
			}else{
				throw_exception('Class {'.$name.'} does not exist.');
			}			
		}else{
			$file_path = ANF_PATH.$file_path.'/'.$class_name.'.class.php';
			throw_exception('Unable to load the file '.$file_path.' , file is not exist.');
			return false;
		}
	}
	
	private static function load_model($class_name, $initialize = 0, $file_path = '') {
		static $models = array();
		if(empty($file_path)) $file_path = 'model';

		$key = md5($file_path.$class_name);
		if(isset($models[$key])){
			if(!empty($models[$key])){
				return $models[$key];
			}else{
				return true;
			}
		}

		if(file_exists(APP_PATH.$file_path.'/'.$class_name.'.class.php')){
			include_once APP_PATH.$file_path.'/'.$class_name.'.class.php';
			$class_name = strtolower($class_name);
			$name = ucfirst($class_name);
			if(class_exists($name)){
				if($initialize){
					$models[$key] = new $name;
				}else{
					$models[$key] = true;
				}
				return $models[$key];
			}else{
				throw_exception('Class {'.$name.'} does not exist.');
			}			
		}else{
			$file_path = APP_PATH.$file_path.'/'.$class_name.'.class.php';
			throw_exception('Unable to load the file '.$file_path.' , file is not exist.');
			return false;
		}
	}
	
	public static function load_config($file_name, $key = '', $default = '', $reload = false){
		static $configs = array();
		if(!$reload && isset($configs[$file_name])){
			if(empty($key)) {
				return $configs[$file_name];
			}elseif(isset($configs[$file_name][$key])){
				return $configs[$file_name][$key];
			}else{
				return $default;
			}
		}
		$file_path = APP_PATH.'config/'.$file_name.'.php';
		if(file_exists($file_path)) {
			$configs[$file_name] = include_once $file_path;
		}else{
			throw_exception('Unable to load the file '.$file_path.' , Config file is not exist.');
		}
		if(empty($key)){
			return $configs[$file_name];
		}elseif(isset($configs[$file_name][$key])){
			return $configs[$file_name][$key];
		}else{			
			return $default;
		}
	}
}