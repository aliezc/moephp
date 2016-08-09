<?php
	class Session{
		public static function get($k){
			return $_SESSION[$k];
		}
		
		public static function set($k, $v){
			$_SESSION[$k] = $v;
			return self;
		}
		
		public static function exists($k){
			return isset($_SESSION[$k]);
		}
		
		public static function get_json($k){
			return json_decode($_SESSION[$k]);
		}
		
		public static function set_json($k, $v){
			$_SESSION[$k] = json_encode($v);
			return self;
		}
		
		public static function remove($k){
			unset($_SESSION[$k]);
			return self;
		}
	}
?>