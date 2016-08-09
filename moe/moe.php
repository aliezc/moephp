<?php
	class MOE{
		
		const CHAR_TABLE = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		
		const METHOD_ALL = 'ALL',
		      METHOD_GET = 'GET',
		      METHOD_POST = 'POST',
		      METHOD_PUT = 'PUT',
		      METHOD_DELETE = 'DELETE';
		// pdo
		public static $db;
		
		// init environment
		// eg. JI::init()
		public static function init(){
			session_start();
			
			// init database connection
			if(defined('DBCONN') && defined('DBUSER') && defined('DBPWD')){
				// if defined these const, initialize database connection
				self::$db = new PDO(DBCONN, DBUSER, DBPWD);
				if(defined('DBUTF8')){
					if(DBUTF8) self::$db->query('set names utf8');
				}
			}
			
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
				if(URLMODE == 0){
					// PATH_INFO
					// http[s]://hostname/index.php/{model}/{sub}/{action}/[/other/request/args]
					// default: index
					// http[s]://hostname/ => /index.php/index/index/main
					self::mvc_pathinfo(Page::server('pathinfo'));
				}else if(URLMODE == 1){
					// rewrite
					// http[s]://hostname/?/{model}/{sub}/{action}/[/other/request/args]
					self::mvc_rewrite();
				}else if(URLMODE == 2){
					// raw
					// http[s]://hostname/?m={model}&s={sub}&a={action}&q=/reqargs
					self::mvc_raw();
				}
			}
		}
		
		private static function mvc_call($file, $class, $arg=array()){
			if(is_file($file)){
				require_once $file;
				if(class_exists($class[0])){
					if(method_exists($class[0], $class[1])){
						forward_static_call_array($class, $arg);
					}
				}
			}
		}
		
		private static function mvc_pathinfo($pathinfo){
			// match rules
			if(preg_match('/^\/([a-zA-Z0-9_]*?)\/([a-zA-Z0-9_]*?)\/([a-zA-Z0-9_]*?)\/(.*)$/', $pathinfo, $m)){
				// match /model/sub/action/[args]
				list( , $model, $sub, $action, $reqargs) = $m;
				
				$arr = explode("/", $reqargs);
				
				self::mvc_call(APPPATH . '/' . $model . '/' . $sub . '.php', array($model . '_' . $sub, $action), $arr);
			}else if(preg_match('/^\/([a-zA-Z0-9_]*?)\/([a-zA-Z0-9_]*?)\/([a-zA-Z0-9_]*?)\/?$/', $pathinfo, $m)){
				// match /model/sub/action[/]
				list( , $model, $sub, $action) = $m;
				
				self::mvc_call(APPPATH . '/' . $model . '/' . $sub . '.php', array($model . '_' . $sub, $action));
			}else if(preg_match('/^\/([a-zA-Z0-9_]*?)\/([a-zA-Z0-9_]*?)\/?$/', $pathinfo, $m)){
				// match /model/sub[/]
				list( , $model, $sub) = $m;
				
				self::mvc_call(APPPATH . '/' . $model . '/' . $sub . '.php', array($model . '_' . $sub, 'index'));
			}else if(preg_match('/^\/([a-zA-Z0-9_]*?)\/?$/', $pathinfo, $m)){
				// match /model[/]
				list( , $model) = $m;
				
				self::mvc_call(APPPATH . '/' . $model . '/index.php', array($model . '_index', 'index'));
			}else{
				// match / or null
				
				self::mvc_call(APPPATH . '/Index/index.php', array('Index_index', 'index'));
			}
		}
		
		// gen mvc url
		public static function url($u){
			if(preg_match('/^https?\:\/\//', $u)) return $u;
			if(URLMODE == 0){
				return SCHEME . '//' . HOST . '/' . INDEX . (preg_match('/^\//', $u) ? '' : '/') . $u;
			}else if(URLMODE == 1){
				return SCHEME . '//' . HOST . '/?' . (preg_match('/^\//') ? '' : '/') . $u;
			}else if(URLMODE == 2){
				$arr = explode('/', (preg_match('/^\//', $u) ? substr($u, 1) : $u));
				
				$url = SCHEME . '//' . HOST . '/?';
				if(count($arr) > 3){
					$url .= 'm=' . $arr[0] . '&s=' . $arr[1] . '&a=' . $arr[2] . '&q=' . ('/' . implode('/', array_slice($arr, 3)));
				}else if(count($arr) == 3){
					$url .= 'm=' . $arr[0] . '&s=' . $arr[1] . '&a=' . $arr[2];
				}else if(count($arr) == 2){
					$url .= 'm=' . $arr[0] . '&s=' . $arr[1];
				}else if(count($arr) == 1){
					$url .= 'm=' . $arr[0];
				}else{
					$url = substr($url, 0, -1);
				}
				return $url;
			}
		}
		
		private static function mvc_rewrite(){
			self::mvc_pathinfo($_SERVER['QUERY_STRING']);
		}
		
		private static function mvc_raw(){
			$model = isset($_GET['m']) ? $_GET['m'] : 'Index';
			$sub = isset($_GET['s']) ? $_GET['s'] : 'index';
			$action = isset($_GET['a']) ? $_GET['a'] : 'index';
			$q = isset($_GET['q']) ? $_GET['q'] : '/';
			
			$s = '/' . $model . '/' . $sub . '/' . $action . $q;
			self::mvc_pathinfo($s);
		}
		
		// excute a sql query
		// eg. $arr = JI::query('select * from user where id=:id', array(":id" => 1), PDO::FETCH_BOTH)
		public static function query($sql, $params=null, $fetch=PDO::FETCH_ASSOC){
			$q = self::$db->prepare($sql);
			$q->execute($params);
			return $q->fetchall($fetch);
		}
		
		// excute a sql query without rows
		// eg. JI::exec('delete from user where id=:id', array(":id" => 1)
		public static function exec($sql, $params=null){
			$q = self::$db->prepare($sql);
			return $q->execute($params) ? self::$db->lastInsertId() : null;
		}
		
		public static function rand_str($length=4){
			$arr = array();
			for($i = 0; $i < $length; $i++){
				array_push($arr, rand(0, 61));
			}
			$str = '';
			for($i = 0; $i < $length; $i++){
				$str .= self::CHAR_TABLE[$arr[$i]];
			}
			return $str;
		}
		
		// gen a upload file name to md5 with extname
		// eg. JI::md5_filaname('image');
		public static function md5_filename($s){
			if(isset($_FILES[$s])){
				$md5 = md5_file($_FILES[$s]['tmp_name']);
				preg_match('/\.([a-zA-Z0-9]*)$/', $_FILES[$s]['name'], $m);
				return $md5 . '.' . $m[1];
			}else{
				return false;
			}
		}
		
		// set a route rule
		// eg. JI::route(JI::METHOD_GET, '/page/{$page}', function($page){ //... });
		public static function route($method=self::METHOD_ALL, $path='/', $func){
			if(!isset($_SERVER['PATH_INFO'])) die('PATH_INFO no opened');
			if(empty($_SERVER['PATH_INFO'])) return false;
			
			// path info
			$pinfo = self::get_request_info('pathinfo');
			
			// request method
			$rmethod = self::get_request_info('method');
			
			if($rmethod == $method || $method = 'ALL' || $rmethod == 'HEAD'){
				// match method, HEAD always matched
				preg_match_all('/\{\$([a-zA-z0-9_]*)\}/', $path, $m);
				if(count($m[1]) > 0){
					// exists dynanic arg
					$args = $m[1];
					
					// set regex
					$reg = preg_replace('/\{\$[a-zA-z0-9_]*\}/', '(.*?)', $path);
					$reg = str_replace("/", "\\/", $reg);
					$reg = '/^' . $reg . '$/';
					
					if(preg_match($reg, $pinfo, $m)){
						// matched
						$cb_args = array_slice($m, 1);
						call_user_func_array($func, $cb_args);
					}
				}else{
					// no dynanic arg
					if($pinfo == $path){
						$func();
						return true;
					}
				}
				
				return false;
			}else{
				return false;
			}
		}
	}
	
	// require
	require_once "route.php";
	require_once "session.php";
	require_once "page.php";
	require_once "db.php";
?>