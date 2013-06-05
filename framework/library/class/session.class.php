<?php
class Session{
	public static $session = '';
	protected $_driver = '';
	protected $_driver_object = '';
	protected $config = array();
	private static $_object = '';
	
	
	protected static function init(){
		self::$session = self::get_instance()->get_driver();	
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
		$this->config = Anframework::load_config('session');
		$_driver_object = Anframework::load_file('session/'.$this->config['type'], 'class', 1);		
		return $_driver_object;
	}	
}