<?php
class Template{
	public static function template_compile($c, $a){
		$tpl_file = $_tpl = APP_PATH.'template/'.$c.'/'.$a.'.html';
		if(!file_exists($tpl_file)){
			throw_exception('Template does not exist.'.'(template/'.$c.'/'.$a.'.html)');
		}
		$content = @file_get_contents($tpl_file);
		$file_path = APP_PATH.'cache/compiled_template/'.$c.'/';
	    if(!is_dir($file_path)) {
			mkdir($file_path, 0777, true);
	    }
		$compiled_tpl_file = $file_path.$a.'.php';
		$content = self::template_parse($content);
		$content = self::compress_html($content);
		$strlen = file_put_contents($compiled_tpl_file, $content);
		chmod($compiled_tpl_file, 0777);
		return $strlen;
	}
	
	public static function compress_css($t){
		$t = preg_replace("/\/\*(.*?)\*\//s", ' ', $t);
		$t = preg_replace("/(\s{2,}|[\r\n|\n|\t|\r])/", ' ', $t);

		$t = preg_replace('/([,|;|:|{|}]) /', '\\1', $t);
		$t = str_replace(' {', '{', $t);

		$t = str_replace(';}', '}', $t);
		$t = str_replace(' }', '}', $t);
	
		return $t;
	}
	
	public static function compress_html($string) {  
		$string = preg_replace("~>\s+\r~", ">", preg_replace("~>\s+\n~", ">", $string));
		$string = preg_replace("~>\s+<~", "><", $string);
		return $string;  
	}
	
	public static function get_front_file($c, $a, $type, $cache = 0){
		$cdn_file = md5($c.$a.$type);
		$cdn_file = CACHE_PATH.'cloud/'.$cdn_file.'.cdn';
		$cdn_content = @file_get_contents($cdn_file);
		if($cdn_content && !$cache){
			echo $cdn_content;
		}else{
			self::get_front_file_by_cloud($c, $a, $type, $cache);
		}
	}
	
	public static function get_front_file_by_cloud($c, $a, $type, $cache = 0){		
	    $cdn_file = md5($c.$a.$type);
		$cdn_file = CACHE_PATH.'cloud/'.$cdn_file.'.cdn';
		$app_key_arr = Anframework::load_config('system');
		$para = 'app_key='.$app_key_arr['app_key'].'&app_secret='.$app_key_arr['app_secret'].'&ac=get_'.$type.'&c='.$c.'&a='.$a.'&app_domain='.$_SERVER['SERVER_NAME'].'&app_ip='.SER_IP;
		$para = str_auth($para, 'ENCODE', SER_IP);
		$_request_url = 'http://oa.woji.net/anoa/api?para='.$para.'&ip='.SER_IP;
		if($cache){
			$cdn_content = @file_get_contents($_request_url.'&is_update=1');
			return $cdn_content;
		}else{
			$cdn_content = @file_get_contents($_request_url);
		}
		if($cdn_content === '403'){			
			throw_exception('System error or network error! Try it again!');
		}
		if(!file_exists($cdn_file)){
			if(!is_dir(CACHE_PATH.'cloud/')) {
				mkdir(CACHE_PATH.'cloud/', 0777, true);
			}
		}
		@file_put_contents($cdn_file, $cdn_content);
		chmod($cdn_file, 0777);
		echo $cdn_content;		
	}	
	
	
	
	public static function template_parse($str){
		$str = preg_replace("/\{css\s+(.+)\}/", "<?php set_front_file(\\1,'css'); ?>", $str);
		$str = preg_replace("/\{js\s+(.+)\}/", "<?php set_front_file(\\1,'js'); ?>", $str);
		$str = preg_replace("/\{template\s+(.+)\}/", "<?php include template(\\1); ?>", $str);
		$str = preg_replace("/\{include\s+(.+)\}/", "<?php include \\1; ?>", $str);
		$str = preg_replace("/\{php\s+(.+)\}/", "<?php \\1?>", $str);
		$str = preg_replace("/\{if\s+(.+?)\}/", "<?php if(\\1) { ?>", $str);
		$str = preg_replace("/\{else\}/", "<?php } else { ?>", $str);
		$str = preg_replace("/\{elseif\s+(.+?)\}/", "<?php } elseif (\\1) { ?>", $str);
		$str = preg_replace("/\{\/if\}/", "<?php } ?>", $str);
		//for 循环
		$str = preg_replace("/\{for\s+(.+?)\}/","<?php for(\\1) { ?>",$str);
		$str = preg_replace("/\{\/for\}/","<?php } ?>",$str);
		//++ --
		$str = preg_replace("/\{\+\+(.+?)\}/","<?php ++\\1; ?>",$str);
		$str = preg_replace("/\{\-\-(.+?)\}/","<?php ++\\1; ?>",$str);
		$str = preg_replace("/\{(.+?)\+\+\}/","<?php \\1++; ?>",$str);
		$str = preg_replace("/\{(.+?)\-\-\}/","<?php \\1--; ?>",$str);
		$str = preg_replace("/\{loop\s+(\S+)\s+(\S+)\}/", "<?php \$n=1;if(is_array(\\1)) foreach(\\1 AS \\2) { ?>", $str);
		$str = preg_replace("/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}/", "<?php \$n=1; if(is_array(\\1)) foreach(\\1 AS \\2 => \\3) { ?>", $str);
		$str = preg_replace("/\{\/loop\}/", "<?php \$n++;}unset(\$n); ?>", $str );
		$str = preg_replace("/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $str);
		$str = preg_replace("/\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $str);
		$str = preg_replace("/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/", "<?php echo \\1;?>", $str);
		$str = preg_replace("/\{(\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/es", "self::addquote('<?php echo \\1;?>')",$str);
		$str = preg_replace("/\{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)\}/s", "<?php echo \\1;?>", $str);
		return $str;
	}
	
	public static function addquote($var){
		return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
	}
}