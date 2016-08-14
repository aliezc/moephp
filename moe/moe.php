<?php
	require_once 'config.php';
	require_once 'const.php';
	
	function moe_init(){
		session_start();
		
		require_once 'lib.php';
		
		// load 3rd part lib
		if(defined('LIBPATH')){
			$path = __DIR__ . '/../' . LIBPATH;
			if(is_dir($path)){
				$d = opendir($path);
				while(false !== ($dir = readdir($d))){
					if($dir == '.' || $dir == '..') continue;
					$s = $path . '/' . $dir;
					if(is_file($s) && preg_match('/\.php$/i', $dir)){
						require_once $s;
					}else if(is_dir($s)){
						$f = $s . '/index.php';
						if(is_file($f)) require_once $f;
					}
				}
			}
		}
		
		// initialize mvc
		if(defined('URLMODE') && defined('APPPATH')){
			if(URLMODE === true){
				// PATH_INFO
				// http[s]://hostname/index.php/{model}/{sub}/{action}/[/other/request/args]
				// default: index
				// http[s]://hostname/ => /index.php/index/index/main
				mvc_pathinfo(moe_path_info());
			}else{
				// raw
				// http[s]://hostname/?m={model}&s={sub}&a={action}&q=/reqargs
				mvc_raw();
			}
		}
	}
	
	function moe_path_info(){
		if(defined('URLMODE')){
			if(URLMODE === true) return $_SERVER['PATH_INFO'];
			else return $_SERVER['QUERY_STRING'];
		}else{
			return $_SERVER['QUERY_STRING'];
		}
	}
	
	function moe_session(){
		$s = func_get_args();
		if(count($s) == 1){
			if(isset($_SESSION[$s[0]])) return $_SESSION[$s[0]];
			return null;
		}else if(count($s) == 2){
			$_SESSION[$s[0]] = $s[1];
			return true;
		}
		
		return null;
	}
	
	function moe_url($u){
		if(preg_match('/^https?\:\/\//i', $u)) return $u;
		if($u == '/') return '/';
		if(URLMODE === true){
			return '/' . INDEX . $u;
		}else{
			return '/?' . $u;
		}
	}
	
	require_once 'page.php';
	require_once 'db.php';
?>