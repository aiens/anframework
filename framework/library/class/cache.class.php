<?php
class Cache{
	public static $cache = '';
	protected $_driver = '';
	protected $_driver_object = '';
	protected $config = array();
	private static $_object = '';

	protected static function init(){
		self::$cache = self::get_instance()->get_driver();	
	}
	
	public static function get_instance(){
		if(self::$_object == ''){
			self::$_object = new self();
		}
		return self::$_object;
	}
	
	public function get_driver(){
		if(!isset($this->_driver) || !is_object($this->_driver)){
			$this->_driver = $this->driver();
		}
		return $this->_driver;
	}
	
	protected function driver() {		
		$this->config = Anframework::load_config('cache');
		switch($this->config['type']){
			case 'file':
				$_driver_object = Anframework::load_file('cache/file_cache', 'class', 1);
				break;
			case 'memcache':
				define('MEMCACHE_HOST', $this->config['hostname']);
				define('MEMCACHE_PORT', $this->config['port']);
				define('MEMCACHE_TIMEOUT', $this->config['timeout']);
				define('MEMCACHE_DEBUG', $this->config['debug']);				
				$_driver_object = Anframework::load_file('cache/memcache');
				break;
			case 'apc' :
				$_driver_object = Anframework::load_file('cache/apc');
				break;
			default :
				$_driver_object = Anframework::load_file('cache/file_cache', 'class', 1);
		}
		return $_driver_object;
	}
	
	protected function close(){
		$this->_driver->close();
	}
}