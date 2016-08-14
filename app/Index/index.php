<?php
	function Index_index_index(){
		$param = array();
		
		$param['title'] = moe_getkv('title');
		$param['zan'] = moe_getkv('good');
		$param['welcome'] = 'Moephp - 轻量级、面向过程的PHP框架';
		$param['welcome2'] = '可能是最卖萌的PHP框架';
		
		$links = moe_select('links');
		foreach($links as $k => $v){
			$links[$k]['url'] = moe_url($links[$k]['url']);
		}
		$param['links'] = moe_render_sub('Index', 'index', 'links', $links);
		
		moe_render('Index', 'index', $param);
	}
?>