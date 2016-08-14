<?php
	function moe_db(){
		if(defined('DBCONN') && defined('DBUSER') && defined('DBPWD')){
			// if defined these const, initialize database connection
			$limit = 10;
			$counter = 0;
			while (true) {
			    try {
			    	if(DBPERSISTENT){
						$db = new PDO(DBCONN, DBUSER, DBPWD, array(PDO::ATTR_PERSISTENT => true));
					}else{
						$db = new PDO(DBCONN, DBUSER, DBPWD);
					}
					if(defined('DBUTF8')){
						if(DBUTF8) $db->query('set names utf8');
					}
					
			        break;
			    }
			    catch (Exception $e) {
			        $db = null;
			        $counter++;
			        if ($counter == $limit)
			            throw $e;
			    }
			}
			
			
			return $db;
		}
	}

	function moe_query($sql, $params=null, $fetch=PDO::FETCH_ASSOC){
		$db = moe_db();
		
		$q = $db->prepare($sql);
		if($q->execute($params)){
			if(preg_match('/^(select|show|explain|describe)/i')){
				return $q->fetchall($fetch);
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
		
		$db = moe_db();
		$q = $db->prepare($sql);
		if($q->execute($params)){
			return $q->fetchall($fetch);
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
		
		$db = moe_db();
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
		
		$db = moe_db();
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
		
		$db = moe_db();
		$q = $db->prepare($sql);
		return $q->execute(array_merge($data, $params));
	}
	
	function moe_prepare($sql){
		$db = moe_db();
		return $db->prepare($sql);
	}
	
	function moe_exec($stmt, $params=array()){
		return $stmt->execute($params);
	}
	
	function moe_rowcount($table, $where='', $params=array()){
		$sql = 'select count(*) as cnt from `' . $table . '`';
		if(!empty($where)) $sql .= ' where ' . $where;
		
		$db = moe_db();
		$q = $db->prepare($sql);
		$q->execute($params);
		$res = $q->fetch(PDO::FETCH_ASSOC);
		return $res['cnt'];
	}
	
	function moe_getkv($k, $table='baseinfo', $key='name', $value='value'){
		$sql = 'select `' . $value . '` from `' . $table . '` where `' . $key . '`=:name';
		
		$db = moe_db();
		$q = $db->prepare($sql);
		if($q->execute(array(":name" => $k))){
			if($res = $q->fetch(PDO::FETCH_ASSOC)) return $res[$value];
			else return null;
		}else{
			return null;
		}
	}
?>