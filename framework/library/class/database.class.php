<?php
class Database{
	public static $db = '';
	protected $_driver = '';
	protected $_driver_object = '';
	protected $config = array();
	private static $_object = '';
		
	protected static function init(){		
		self::$db = self::get_instance()->get_driver();	
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
	
	protected function driver(){
		$this->config = Anframework::load_config('database');
		switch($this->config['type']){
			case 'mysql' :
				Anframework::load_file('database/mysql');
				$_driver_object = new mysql();
				break;
			case 'mysqli' :
				$_driver_object = Anframework::load_file('database/mysqli');
				break;
			case 'access' :
				$_driver_object = Anframework::load_file('database/db_access');
				break;
			default :
				Anframework::load_file('database/mysql');
				$_driver_object = new mysql();
		}
		$_driver_object->open($this->config);
		return $_driver_object;
	}
	
	protected function close(){
		$this->_driver->close();
	}
}