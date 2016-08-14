<?php
	function http_404(){
		$param = array("title" => "找不到哦～", "body" => "404 - 找不到哦～");
		moe_render('404', 'index', $param);
	}
?>