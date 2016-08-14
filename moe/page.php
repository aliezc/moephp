<?php
	function moe_headers($a){
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
	
	function moe_json($o){
		moe_headers(array("Content-type" => "application/json"));
		echo json_encode($o);
	}
	
	function query_exists($a){
		if(is_array($a)){
			foreach($a as $d){
				if(!isset($_GET[$d])) return false;
			}
		}else{
			if(!isset($_GET[$a])) return false;
		}
		
		return true;
	}
	
	function moe_render_sub($m, $v, $s, $a=array()){
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
					
					$tmp = preg_replace('/\{\$.*\}/', '', $tmp);
					$result .= $tmp;
				}
			}else{
				$result = $str;
				foreach($a as $k => $b){
					$result = str_replace('{$' . $k . "}", $b, $result);
				}
				$result = preg_replace('/\{\$.*\}/', $b, $result);
			}
		}
		
		return $result;
	}
	
	function moe_render($m, $v, $o=array()){
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
?>