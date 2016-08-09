<?php
	class Index_index{
		const MODEL = 'Index';
		
		const SUB = 'index';
		
		public static function index(){
			// index page
			
			$links = DB::select(array("table" => "links"));
			
			for($i = 0; $i < count($links); $i++){
				$links[$i]['url'] = MOE::url($links[$i]['url']);
			}
			
			$sub = Page::sub(self::MODEL, self::SUB, 'links', $links);
			Page::html(self::MODEL, self::SUB, array("title" => DB::getkv('title'),
				"welcome" => "欢迎使用Moephp",
				"welcome2" => "可能是最萌的PHP框架 (￣ω￣)<sup>+</sup>",
				"good" => DB::getkv('good'),
				"zanurl" => MOE::url('/Index/index/zan'),
				"links" => $sub));
		}
		
		public static function zan(){
			if(!isset($_COOKIE['good'])){
				$good = DB::getkv('good');
				$good++;
				DB::update(array("table" => "baseinfo", "where" => "`name`=:name", "params" => array(":name" => 'good')), array(":value" => $good));
				// ok
				setcookie('good', '1', time() + 86400, '/', HOST);
				Page::json(array("result" => 0, "good" => $good));
				return;
			}
			
			Page::json(array("result" => 1));
		}
	}
?>