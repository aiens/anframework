<?php
class Session_mysql{
	private $db,$lifetime;
	
	public function __construct(){
		Anframework::load_file('session_model', 'model');		
		$this->db = new Session_model();
		//echo 'Session_mysql __construct';
		$this->lifetime = Anframework::load_config('system','session_ttl');
    	session_set_save_handler(array(&$this,'open'), array(&$this,'close'), array(&$this,'read'), array(&$this,'write'), array(&$this,'destroy'), array(&$this,'gc'));
    	session_start();
    }
	
	public function open($save_path, $session_name){
		return true;
    }
	
	public function close(){
        return $this->gc($this->lifetime);
    }
	
	public function read($id){
		$r = $this->db->get_one(array('sessionid'=>$id), 'data');
		return $r ? $r['data'] : '';
    }
	
	public function write($id, $data){
    	$uid = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
    	$roleid = isset($_SESSION['roleid']) ? $_SESSION['roleid'] : 0;
    	$groupid = isset($_SESSION['groupid']) ? $_SESSION['groupid'] : 0;
		$c = defined('ANF_C') ? ANF_C : '';
		$a = defined('ANF_A') ? ANF_A : '';
		if(strlen($data) > 255) $data = '';
		$ip = IP;
		$sessiondata = array(
							'sessionid'=>$id,
							'userid'=>$uid,
							'ip'=>$ip,
							'lastvisit'=>SYS_TIME,
							'roleid'=>$roleid,
							'groupid'=>$groupid,
							'c'=>$c,
							'a'=>$a,
							'data'=>$data,
						);
		return $this->db->insert($sessiondata, 1, 1);
    }
	
	public function destroy($id){
		return $this->db->delete(array('sessionid'=>$id));
    }
	
	public function gc($maxlifetime){
		$expiretime = SYS_TIME - $maxlifetime;
		return $this->db->delete("`lastvisit`<$expiretime");
    }
	
	public function test(){
		echo 'Session driver has been loaded.'; 
	}
}