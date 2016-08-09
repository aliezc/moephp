<?php
	class Route{
		const METHOD_ALL = 'ALL',
		      METHOD_GET = 'GET',
		      METHOD_POST = 'POST',
		      METHOD_PUT = 'PUT',
		      METHOD_DELETE = 'DELETE';
		// eg. Route::get('/page/{$page}', function($page){ //... });
		public static function get($path, $func){
			return MOE::route(MOE::METHOD_GET, $path, $func);
		}
		
		public static function post($path, $func){
			return MOE::route(MOE::METHOD_POST, $path, $func);
		}
		
		public static function put($path, $func){
			return MOE::route(MOE::METHOD_PUT, $path, $func);
		}
		
		public static function delete($path, $func){
			return MOE::route(MOE::METHOD_DELETE, $path, $func);
		}
	}
?>