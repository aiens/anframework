<?php
class Model{
	public $_db;
	protected $config = '';
	protected $table_name = '';
	public $db_tablepre = '';
	public function __construct(){
		$this->config = Anframework::load_config('database');
		$this->db_tablepre = $this->config['tablepre'];
		!defined('DB_TABLEPRE') && define('DB_TABLEPRE',$this->db_tablepre);
		$this->table_name = $this->db_tablepre.$this->table_name;
		$this->_db = Database::get_instance()->get_driver();		
		//$this->_cache = Cache::get_instance()->get_driver();
		//$this->_session = Session::get_instance()->get_driver();
	}
	
	final public function select($where = '', $data = '*', $limit = '', $order = '', $group = '', $key=''){
		if (is_array($where)) $where = arr_to_sqls($where);
		return $this->_db->select($data, $this->table_name, $where, $limit, $order, $group, $key);
	}
	
	final public function lists($where = '', $order = '', $page = 1, $pagesize = 20, $key='', $setpages = 10,$urlrule = '',$array = array()){
		$where = arr_to_sqls($where);
		$this->number = $this->count($where);
		$page = max(intval($page), 1);
		$offset = $pagesize*($page-1);
		$this->pages = pages($this->number, $page, $pagesize, $urlrule, $array, $setpages);
		$array = array();
		if($this->number > 0){
			return $this->select($where, '*', "$offset, $pagesize", $order, '', $key);
		}else{
			return array();
		}
	}
	
	final public function lists_sql($data = '',$sql='',$order='', $page = 1, $pagesize = 20, $setpages = 10,$urlrule = '',$array = array()) {
			$this->number = $this->_db->count_sql($sql);
			$page = max(intval($page), 1);
			$offset = $pagesize*($page-1);
			$this->pages = pages($this->number, $page, $pagesize, $urlrule, $array, $setpages);
			$array = array();
			if ($this->number > 0) {
					return $this->_db->select_sql($data, $sql, $order, "$offset, $pagesize");
			} else {
					return array();
			}
	}	
	
	final public function get_one($where = '', $data = '*', $order = '', $group = ''){
		if(is_array($where)) $where = arr_to_sqls($where);
		return $this->_db->get_one($data, $this->table_name, $where, $order, $group);
	}
	
	final public function query($sql){
		$sql = str_replace('anf_', $this->db_tablepre, $sql);
		return $this->_db->query($sql);
	}
	
	final public function insert($data, $return_insert_id = false, $replace = false){
		return $this->_db->insert($data, $this->table_name, $return_insert_id, $replace);
	}
	
	final public function insert_id(){
		return $this->_db->insert_id();
	}
	
	final public function update($data, $where = ''){
		if(is_array($where)) $where = arr_to_sqls($where);
		return $this->_db->update($data, $this->table_name, $where);
	}
	
	final public function delete($where){
		if(is_array($where)) $where = arr_to_sqls($where);
		return $this->_db->delete($this->table_name, $where);
	}
	
	final public function count($where = ''){
		$r = $this->get_one($where, "COUNT(*) AS num");
		return $r['num'];
	}
	
	final public function affected_rows(){
		return $this->_db->affected_rows();
	}
	
	final public function get_primary(){
		return $this->_db->get_primary($this->table_name);
	}
	
	final public function get_fields($table_name = ''){
		if(empty($table_name)){
			$table_name = $this->table_name;
		}else{
			$table_name = $this->_db_tablepre.$table_name;
		}
		return $this->_db->get_fields($table_name);
	}
	
	final public function table_exists($table){
		return $this->_db->table_exists($this->_db_tablepre.$table);
	}
	
	public function field_exists($field){
		$fields = $this->_db->get_fields($this->table_name);
		return array_key_exists($field, $fields);
	}
	
	final public function list_tables(){
		return $this->_db->list_tables();
	}
	
	final public function fetch_array(){
		$data = array();
		while($r = $this->_db->fetch_next()){
			$data[] = $r;		
		}
		return $data;
	}
	
	final public function version(){
		return $this->_db->version();
	}	
}