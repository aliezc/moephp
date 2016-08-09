<?php
	class Page{
		const SERVER_KEY = array("method" => "REQUEST_METHOD",
			"cookie" => "HTTP_COOKIE",
			"language" => "HTTP_ACCEPT_LANGUAGE",
			"encoding" => "HTTP_ACCEPT_ENCODING",
			"accept" => "HTTP_ACCEPT",
			"ua" => "HTTP_USER_AGENT",
			"cachecontrol" => "HTTP_CACHE_CONTROL",
			"host" => "HTTP_HOST",
			"hostname" => "SERVER_NAME",
			"remoteip" => "REMOTE_ADDR",
			"script" => "SCRIPT_NAME",
			"scriptfile" => "SCRIPT_FILENAME",
			"requesttime" => "REQUEST_TIME",
			"requesttimef" => "REQUEST_TIME_FLOAT",
			"pathinfo" => "PATH_INFO");
		
		// output a json text
		public static function json($o){
			self::header(array("Content-type" => "application/json"));
			echo json_encode($o);
		}
		
		// render mvc html template
		// eg. Page::html('Index', 'index', array("title" => "sb"));
		public static function html($m, $v, $o){
			$file = __DIR__ . '/../app/' .$m . '/view/' . $v . '.htm';
			if(is_file($file)){
				$str = file_get_contents($file);
				foreach($o as $k => $v){
					$str = str_replace('{$' . $k . '}', $v, $str);
				}
				// clear space
				$str = preg_replace('/\{\$.*\}/', '', $str);
				$str = preg_replace('/>\s*</m', '><', $str);
				echo $str;
			}
		}
		
		// render mvc html template part code
		// eg. page::sub('Index', 'index', 'list', array(array("title" => "a")));
		public static function sub($m, $v, $s, $a){
			$file = __DIR__ . '/../app/' .$m . '/view/' . $v . '.' . $s . '.htm';
			$result = '';
			if(is_file($file)){
				$str = file_get_contents($file);
				if(is_array($a[0])){
					for($i = 0; $i < count($a); $i++){
						$tmp = $str;
						foreach($a[$i] as $k => $b){
							$tmp = str_replace('{$' . $k . "}", $b, $tmp);
						}
						$result .= $tmp;
					}
				}else{
					$result = $str;
					foreach($a as $k => $b){
						$result = str_replace('{$' . $k . "}", $b, $result);
					}
				}
			}
			
			return $result;
		}
		
		// output headers
		// eg. Page::header(array("content-type" => "text/css", "server" => "example"));
		public static function header($a){
			foreach($a as $k => $v){
				if(is_numeric($k)){
					// maybe status code, eg. 200 OK
					header($k . ' ' . $v);
				}else{
					// eg. Content-type: application/json
					header($k . ': ' . $v);
				}
			}
		}
		
		public static function get($k){
			return $_GET[$k] || null;
		}
		
		public static function post($k){
			return $_POST[$k] || null;
		}
		
		public static function request($k){
			return $_REQUEST[$k] || null;
		}
		
		public static function file($k){
			return $_FILES[$k] || null;
		}
		
		public static function server($k){
			return $_SERVER[self::SERVER_KEY[$k]];
		}
		
		public static function keys(){
			return array_keys(self::SERVER_KEY);
		}
		
		public static function query_exists($a){
			if(is_array($a)){
				foreach($a as $d){
					if(!isset($_GET[$d])) return false;
				}
			}else{
				if(!isset($_GET[$a])) return false;
			}
			
			return true;
		}
	}
?>