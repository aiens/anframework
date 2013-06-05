<?php
class Index extends Controller{	
	
	public function init(){		
		$title = 'hello world!';
		$h1 = 'hello AnFramework!';
		$welcome = 'hello world,this is default page!';
		define('APP_VERSION', Anframework::load_config('system', 'version'));
		define('APP_RELEASE', Anframework::load_config('system', 'release'));
		$powered_by = 'Powered by AnFramework Version '.ANF_VERSION.' Release'.ANF_RELEASE;
		$app_info = 'This App Version '.APP_VERSION.' Release'.APP_RELEASE;
		$_class_list = get_declared_classes();
		$start_count_flag = false;
		$class_list = array();
		foreach($_class_list as $val){
			if($val == 'Anframework'){
				$start_count_flag = true;
			}
			if($start_count_flag){
				$class_list[] = $val;
			}
		}
		$class_total = count($class_list);
		$function_list = get_defined_functions();
		$function_list = $function_list['user'];
		$function_total = count($function_list);		
		$_defined_list = get_defined_constants();
		$start_count_flag = false;
		$defined_list = array();
		foreach($_defined_list as $key => $val){
			if($key == 'ROOT_PATH'){
				$start_count_flag = true;
			}
			if($start_count_flag){
				$defined_list[$key] = $val;
			}
		}
		$defined_total = count($defined_list);
		$file_list = get_included_files();
		$file_total = count($file_list);
		$var_list = get_defined_vars();
		$var_total = count($var_list);
		include template(ANF_C);
	}	
	
}