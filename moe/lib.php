<?php
	// lib func
	function mvc_call($file, $func, $arg=array()){
		if(is_file($file)){
			require_once $file;
			if(function_exists($func)){
				call_user_func_array($func, $arg);
			}else{
				mvc_call_code(404);
			}
		}else{
			mvc_call_code(404);
		}
	}
	
	function mvc_call_code($code){
		http_response_code($code);
		$path = __DIR__ . '/../' . APPPATH . '/' . $code;
		if(is_dir($path)){
			if(is_file($path . '/index.php')){
				require_once $path . '/index.php';
				
				if(function_exists('http_' . $code)){
					call_user_func('http_' . $code);
				}
			}
		}
	}
	
	function mvc_pathinfo($pathinfo){
		// match rules
		if(preg_match('/^\/([a-zA-Z0-9_]*?)\/([a-zA-Z0-9_]*?)\/([a-zA-Z0-9_]*?)\/(.*)$/', $pathinfo, $m)){
			// match /model/sub/action/[args]
			list( , $model, $sub, $action, $reqargs) = $m;
			
			$arr = explode("/", $reqargs);
			
			mvc_call(APPPATH . '/' . $model . '/' . $sub . '.php', $model . '_' . $sub . '_' . $action, $arr);
		}else if(preg_match('/^\/([a-zA-Z0-9_]*?)\/([a-zA-Z0-9_]*?)\/([a-zA-Z0-9_]*?)\/?$/', $pathinfo, $m)){
			// match /model/sub/action[/]
			list( , $model, $sub, $action) = $m;
			
			mvc_call(APPPATH . '/' . $model . '/' . $sub . '.php', $model . '_' . $sub . '_' . $action);
		}else if(preg_match('/^\/([a-zA-Z0-9_]*?)\/([a-zA-Z0-9_]*?)\/?$/', $pathinfo, $m)){
			// match /model/sub[/]
			list( , $model, $sub) = $m;
			
			mvc_call(APPPATH . '/' . $model . '/' . $sub . '.php', $model . '_' . $sub . '_index');
		}else if(preg_match('/^\/([a-zA-Z0-9_]*?)\/?$/', $pathinfo, $m)){
			// match /model[/]
			list( , $model) = $m;
			
			mvc_call(APPPATH . '/' . $model . '/index.php', $model . '_index_index');
		}else{
			// match / or null
			
			mvc_call(APPPATH . '/Index/index.php', 'Index_index_index');
		}
	}
	
	function mvc_raw(){
		mvc_pathinfo($_SERVER['QUERY_STRING']);
	}
?>