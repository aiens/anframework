<?php
class Anexception extends Exception {
	/**
     * 异常类型
     * @var string
     * @access private
     */
    private $type;
	// 是否存在多余调试信息
	private $extra;
	/**
     * 架构函数
     * @access public
     * @param string $message  异常信息
     */
    public function __construct($message, $code = 0, $extra = false) {
		parent::__construct($message,$code);
        $this->type = get_class($this);
        $this->extra = $extra;
    }
 
    /**
     * 异常输出 所有异常处理类均通过__toString方法输出错误
     * 该方法可以被子类重载
     * @access public
     * @return array
     */
    public function __toString() {
		$trace = $this->getTrace();
        if($this->extra){
			//通过throw_exception抛出的异常要去掉多余的调试信息
            array_shift($trace);
		}
		$this->class    =   isset($trace[0]['class'])?$trace[0]['class']:'';
        $this->function =   isset($trace[0]['function'])?$trace[0]['function']:'';
        $this->file     =   $trace[0]['file'];
        $this->line     =   $trace[0]['line'];
        $file           =   file($this->file);
        $trace_info      =   '';
        $time = date('y-m-d H:i:m');
        foreach($trace as $t){
			$trace_info .= '['.$time.'] '.$t['file'].' ('.$t['line'].') ';
            $trace_info .= $t['class'].$t['type'].$t['function'].'(';
            $trace_info .= implode(', ', $t['args']);
            $trace_info .=")\n";
        }
		$error['message']   = $this->message;
        $error['type']      = $this->type;
        $error['detail']    =   ($this->line-2).': '.$file[$this->line-3];
        $error['detail']   .=   ($this->line-1).': '.$file[$this->line-2];
        $error['detail']   .=   '<font color="#FF6600" >'.($this->line).': <strong>'.$file[$this->line-1].'</strong></font>';
        $error['detail']   .=   ($this->line+1).': '.$file[$this->line];
        $error['detail']   .=   ($this->line+2).': '.$file[$this->line+1];
        $error['class']     =   $this->class;
        $error['function']  =   $this->function;
        $error['file']      =   $this->file;
        $error['line']      =   $this->line;
        $error['trace']     =   $trace_info; 
        
		return $error;
	}
}