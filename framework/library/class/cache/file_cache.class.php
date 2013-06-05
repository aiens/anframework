<?php
class File_cache{
	protected $_setting = array();
	protected $_config = array();
	
	public function __construct(){
	}
	
	private function _init(){
		$this->_config = Anframework::load_config('cache');
		$this->_setting = $this->_config['file_cache'];
	}
	
	public function set($name, $data, $path = ANF_C){
		$this->_init();
		if(empty($path)){
			$path = ANF_C;
			$file_path = CACHE_PATH.'controller_'.$path.'/';
		}else{
			$file_path = $path.'/';
		}
		$file_name = $name.$this->_setting['suf'];
	    if(!is_dir($file_path)) {
			mkdir($file_path, 0777, true);
	    }
	    
	    if($this->_setting['type'] == 'array') {
	    	$data = "<?php\nreturn ".var_export($data, true).";\n?>";
	    } elseif($this->_setting['type'] == 'serialize') {
	    	$data = serialize($data);
	    }	    
	    
	    //是否开启互斥锁
		if(Anframework::load_config('system', 'lock_ex')) {
			$file_size = file_put_contents($file_path.$file_name, $data, LOCK_EX);
		} else {
			$file_size = file_put_contents($file_path.$file_name, $data);
		}
	    
	    return $file_size ? $file_size : 'false';
	}
	
	public function get($name, $path = ANF_C){
		$this->_init();
		if(empty($path)){
			$path = ANF_C;
			$file_path = CACHE_PATH.'controller_'.$path.'/';
		}else{
			$file_path = $path.'/';
		}
		$file_name = $name.$this->_setting['suf'];
		if(!file_exists($file_path.$file_name)){
			return false;
		}else{
		    if($this->_setting['type'] == 'array'){
		    	$data = @require($file_path.$file_name);
		    }elseif($this->_setting['type'] == 'serialize'){
		    	$data = unserialize(file_get_contents($file_path.$file_name));
		    }		    
		    return $data;
		}
	}
	
	public function close(){
	}
}