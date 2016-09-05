<?php
function moe_route($rule, $script){
    $path = moe_pathinfo();
    $rule = str_replace('/', '\/', $rule);
    $rule = preg_replace('/\$[a-zA-Z0-9_]+/', '[a-z0-9A-Z%_]+', $rule);
    $rule = '/^' . $rule . '$/';
    if(!preg_match($rule, $path)) return false;
    if(defined('ROUTED')) return false;
    define('ROUTED', true);
    require_once $script;
}

function moe_pathinfo(){
    if(defined('PATHINFO')){
        return empty($_SERVER['PATH_INFO']) ? '/' : $_SERVER['PATH_INFO'];
    }else{
        return empty($_SERVER['QUERY_STRING']) ? '/' : $_SERVER['QUERY_STRING'];
    }
}

function moe_pathargs($rule){
    $rule = str_replace('/', '\/', $rule);
    $rule = preg_replace('/\$[a-zA-Z0-9_]+/', '([a-z0-9A-Z%_]+)', $rule);
    $rule = '/^' . $rule . '$/';
    preg_match($rule, moe_pathinfo(), $m);
    return array_slice($m, 1);
}

function moe_url($url){
    if($url == '/') return $url;
    if(!defined('PATHINFO')) return '/?' . $url;
    if(PATHINFO) return '/index.php' . $url;
    return '/?' . $url;
}

function moe_pdo(){
    return new PDO('mysql:host=' . MYSQL_HOST . ';charset=utf8;dbname=' . MYSQL_DATABASE, MYSQL_USERNAME, MYSQL_PASSWORD);
}

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

function moe_session(){
	$s = func_get_args();
	if(count($s) == 1){
		if(isset($_SESSION[$s[0]])) return $_SESSION[$s[0]];
		return null;
	}else if(count($s) == 2){
		$_SESSION[$s[0]] = $s[1];
		return true;
	}
	
	return null;
}

function moe_render($view, $args){
	$file = $view . '.htm';
	$str = file_get_contents($file);
	foreach($args as $k => $v){
		if(is_array($v)){
			if(preg_match('/\@' . $k . '\[\[(.*?)\]\]/s', $str, $m)){
				$t = $m[1];
				$s = '';
				for($i = 0; $i < count($v); $i++){
					$tmp = $t;
					foreach($v[$i] as $kk => $vv){
						$tmp = str_replace('{$' . $kk . '}', $vv, $tmp);
					}
					$s .= $tmp;
				}
				$str = preg_replace('/\@' . $k . '\[\[.*?\]\]/s', $s, $str);
			}
		}else if(is_string($v)){
			$str = str_replace('{$' . $k . '}', $v, $str);
		}
	}
	
	$str = preg_replace('/\{\$[a-zA-Z0-9_]+\}/', '', $str);
	$str = preg_replace('/\@[a-zA-Z0-9_]\[\[.*?\]\]/m', '', $str);
	$str = preg_replace('/\>\s*?\</m', '><', $str);
	echo $str;
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

function moe_query($sql, $params=null, $fetch=PDO::FETCH_ASSOC){
	$db = moe_pdo();
	
	$q = $db->prepare($sql);
	if($q->execute($params)){
		if(preg_match('/^(select|show|explain|describe)/i', $sql)){
			return $q->fetchall($fetch);
		}else{
			return true;
		}
	}else{
		return false;
	}
}

function moe_stmt($sql, $params=null, $fetch=PDO::FETCH_ASSOC){
	$db = moe_pdo();
	
	$q = $db->prepare($sql);
	if($q->execute($params)){
		return $q;
	}else{
		return false;
	}
}

function moe_one($sql, $params=null, $fetch=PDO::FETCH_ASSOC){
	$db = moe_pdo();
	
	$q = $db->prepare($sql);
	if($q->execute($params)){
		if(preg_match('/^(select|show|explain|describe)/i', $sql)){
			return $q->fetch($fetch);
		}else{
			return true;
		}
	}else{
		return false;
	}
}

function moe_select($table, $params=array(), $select='*', $where='', $order='', $limit='', $fetch=PDO::FETCH_ASSOC){
	$sql = 'select ' . $select . 'from `' . $table . '`';
	if(!empty($where)) $sql .= ' where ' . $where;
	if(!empty($order)) $sql .= ' order by ' . $order;
	if(!empty($limit)) $sql .= ' limit ' . $limit;
	
	$db = moe_pdo();
	$q = $db->prepare($sql);
	if($q->execute($params)){
		return $q->fetchall($fetch);
	}else{
		return null;
	}
}
function moe_sone($table, $params=array(), $select='*', $where='', $order='', $limit='', $fetch=PDO::FETCH_ASSOC){
	$sql = 'select ' . $select . 'from `' . $table . '`';
	if(!empty($where)) $sql .= ' where ' . $where;
	if(!empty($order)) $sql .= ' order by ' . $order;
	if(!empty($limit)) $sql .= ' limit ' . $limit;
	
	$db = moe_pdo();
	$q = $db->prepare($sql);
	if($q->execute($params)){
		return $q->fetch($fetch);
	}else{
		return null;
	}
}

function moe_insert($table, $data){
	$sql = 'insert into `' . $table . '`(';
	$a = $b = '';
	$keys = array_keys($data);
	foreach($keys as $k){
		$a .= '`' . substr($k, 1) . '`,';
		$b .= $k . ',';
	}
	$sql .= substr($a, 0, -1) . ') values (' . substr($b, 0, -1) . ')';
	
	$db = moe_pdo();
	$q = $db->prepare($sql);
	if($q->execute($data)){
		return $db->lastInsertId();
	}else{
		return false;
	}
}

function moe_delete($table, $where='', $params=array()){
	$sql = 'delete from `' . $table . '`';
	if(!empty($where)) $sql .= ' where ' . $where;
	
	$db = moe_pdo();
	$q = $db->prepare($sql);
	return $q->execute($params);
}

function moe_update($table, $where='', $data=array(), $params = array()){
	$sql = 'update `' . $table . '` set ';
	$s = '';
	$keys = array_keys($data);
	foreach($keys as $k){
		$s .= '`' . substr($k, 1) . '`=' . $k . ',';
	}
	$sql .= substr($s, 0, -1);
	if(!empty($where)) $sql .= ' where ' . $where;
	
	$db = moe_pdo();
	$q = $db->prepare($sql);
	return $q->execute(array_merge($data, $params));
}

function moe_prepare($sql){
	$db = moe_pdo();
	return $db->prepare($sql);
}

function moe_exec($stmt, $params=array()){
	return $stmt->execute($params);
}

function moe_rowcount($table, $where='', $params=array()){
	$sql = 'select count(*) as cnt from `' . $table . '`';
	if(!empty($where)) $sql .= ' where ' . $where;
	
	$db = moe_pdo();
	$q = $db->prepare($sql);
	$q->execute($params);
	$res = $q->fetch(PDO::FETCH_ASSOC);
	return $res['cnt'];
}

function moe_getkv($k, $table='baseinfo', $key='name', $value='value'){
	$sql = 'select `' . $value . '` from `' . $table . '` where `' . $key . '`=:name';
	
	$db = moe_pdo();
	$q = $db->prepare($sql);
	if($q->execute(array(":name" => $k))){
		if($res = $q->fetch(PDO::FETCH_ASSOC)) return $res[$value];
		else return null;
	}else{
		return null;
	}
}

function moe_setkv($k, $v, $table='baseinfo', $key='name', $value='value'){
	$sql = '';
	if(moe_rowcount($table, '`' . $key . '`=:k', [':k' => $k]))
		$sql = 'update `' . $table . '` set `' . $value . '`=:v where `' . $key . '`=:k';
	else
		$sql = 'insert into `' . $table . '`(`' . $key . '`, `' . $value . '`) values (:k,:v)';
	
	$db = moe_pdo();
	$q = $db->prepare($sql);
	return $q->execute(array(":k" => $k, ':v' => $v));
}
?>