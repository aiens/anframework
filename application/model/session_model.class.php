<?php
class Session_model extends Model{
	public function __construct(){
		$this->table_name = 'session';		
		parent::__construct();
	}
}