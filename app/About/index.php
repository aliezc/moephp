<?php
	class About_index{
		const MODEL = 'About';
		
		const SUB = 'index';
		
		public static function index(){
			// index page
			Page::html(self::MODEL, self::SUB, array("title" => DB::getkv('title')));
		}
	}
?>