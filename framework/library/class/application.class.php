<?php
class Application{
	public static function init(){
		$_router_arr = Router::get();			
		define('ANF_C', $_router_arr['controller']);
		define('ANF_A', $_router_arr['action']);
		$controller = self::load_controller();	
		if(method_exists($controller, ANF_A)){
			if(preg_match('/^[_]/i', ANF_A)){
				throw_exception('You are visiting the action is to protect the private action.');
			}else{
				call_user_func(array($controller, ANF_A));
			}
		}else{
			throw_exception('Action ['.ANF_A.'] does not exist.');
		}
	}
	
	public static function load_controller(){
		$file_path = APP_PATH.'controller/'.ANF_C.'.php';
		$class_name = ANF_C;
		if(file_exists($file_path)){			
			include $file_path;
			if(class_exists($class_name)){
				return new $class_name;
			}else{
				throw_exception('Controller {'.ANF_C.'} does not exist.');
 			}			
		}else{
			throw_exception('Unable to load the file '.$file_path.' , file is not exist.');
		}
	}
}