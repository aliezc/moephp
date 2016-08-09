<?php
	class DB{
		// query database use object
		// eg. JI::select(array("table" => "user", "select" => array("name", "age"));
		public static function select($o, $fetch=PDO::FETCH_ASSOC){
			$sql = 'select ';
			
			// select
			if($o['select']){
				$arr = explode(',', $o['select']);
				for($i = 0; $i < count($arr); $i++){
					if($i == count($arr) - 1){
						$sql .= $arr[$i] == '*' ? $arr[$i] : ('`' . $arr[$i] . '`');
					}else{
						$sql .= $arr[$i] == '*' ? ($arr[$i] . ',') : ('`' . $arr[$i] . '`,');
					}
				}
			}else{
				$sql .= '*';
			}
			
			// from
			$sql .= ' from `' . $o['table'] . '`';
			
			// where
			if(isset($o['where'])){
				$sql .= ' where' . $o['where'];
			}
			
			// order
			if(isset($o['order'])){
				$sql .= ' order by ' . $o['order'];
			}
			
			// limit
			if(isset($o['limit'])){
				$sql .= ' limit ' . $o['limit'];
			}
			
			return MOE::query($sql, isset($o['params']) ? $o['params'] : null);
		}
		
		// insert data use object
		// eg. JI::insert(array("table" => "user", "format" => array("k", "v")), array("k" => "sb", "v" => "bb"));
		public static function insert($o, $data){
			$sql = 'insert into ' . $o['table'];
			$fmt = $o['format'];
			
			// table format
			if($fmt){
				$sql .= '(';
				for($i = 0; $i < count($fmt); $i++){
					$sql .= '`' . $fmt[$i] . '`';
				}
				$sql .= ')';
			}
			
			$sql .= ' values (';
			
			// table vals
			$l = '';
			foreach($data as $k => $v){
				$l .= ':' . $k . ',';
			}
			
			$sql .= substr($l, 0, -1) . ')';
			
			$q = MOE::$db->prepare($sql);
			return $q->execute($params) ? self::$db->lastInsertId() : null;
		}
		
		// insert multi data
		// like insert and data is array in array
		public static function inserts($o, $data){
			$sql = 'insert into ' . $o['table'];
			$fmt = $o['format'];
			
			// table format
			if($fmt){
				$sql .= '(';
				for($i = 0; $i < count($fmt); $i++){
					$sql .= '`' . $fmt[$i] . '`';
				}
				$sql .= ')';
			}
			
			$sql .= ' values (';
			
			// table vals
			$l = '';
			foreach($data as $k => $v){
				$l .= ':' . $k . ',';
			}
			
			$sql .= substr($l, 0, -1) . ')';
			
			$q = MOE::$db->prepare($sql);
			$arr = array();
			foreach($data as $d){
				if($q->execute($d)){
					array_push($arr, MOE::$db->lastInsertId());
				}
			}
			return $arr;
		}
		
		public static function update($o, $data){
			$sql = 'update ';
			$sql .= '`' . $o['table'] . '` set ';
			
			// set
			foreach($data as $d => $v){
				$k = substr($d, 1);
				$sql .= '`' . $k . '`=' . $d . ',';
			}
			$sql = substr($sql, 0, -1);
			
			// where
			if($o['where']){
				$sql .= ' where ' . $o['where'];
			}
			
			$q = MOE::$db->prepare($sql);
			
			// param
			$p = $o['params'] ? array_merge($data, $o['params']) : $data;
			
			return $q->execute($p) ? MOE::$db->lastInsertId() : null;
		}
		
		public static function delete($o){
			$sql = 'delete from ';
			$sql .= '`' . $o['table'];
			
			// where
			if($o['where']){
				$sql .= ' where ' . $o['where'];
			}
			
			$q = MOE::$db->prepare($sql);
			
			return $q->execute($o['params']);
		}
		
		public static function select_page($o, $start=1, $count=10, $fetch=PDO::FETCH_ASSOC){
			$sql = 'select ';
			
			// select
			if($o['select']){
				$arr = explode($o['select']);
				for($i = 0; $i < count($arr); $i++){
					if($i == count($arr) - 1){
						$sql .= $arr[$i] == '*' ? $arr[$i] : ('`' . $arr[$i] . '`');
					}else{
						$sql .= $arr[$i] == '*' ? ($arr[$i] . ',') : ('`' . $arr[$i] . '`,');
					}
				}
			}else{
				$sql .= '*';
			}
			
			// from
			$sql .= ' from `' . $o['table'] . '`';
			
			// where
			if(isset($o['where'])){
				$sql .= ' where' . $o['where'];
			}
			
			// order
			if(isset($o['order'])){
				$sql .= ' order by ' . $o['order'];
			}
			
			$p = ($start - 1) * $count;
			$limit = $p . ',' . $count;
			$sql .= ' limit ' . $limit;
			
			return MOE::query($sql, isset($o['params']) ? $o['params'] : null);
		}
		
		public static function getkv($k, $table="baseinfo", $kn="name", $vn="value"){
			$sql = 'select `' . $vn . '` from `' . $table . '` where `' . $kn . '`=:name';
			$tmp = MOE::query($sql, array(":name" => $k));
			if($tmp[0]){
				return $tmp[0][$vn];
			}else{
				return null;
			}
		}
		
		public static function get_pages($table, $count=10, $where='', $params=null){
			$sql = 'select count(*) as n from `' . $table . '`';
			if(!empty($where)) $sql .= ' where ' . $where;
			
			$tmp = MOE::query($sql, $params);
			$length = $tmp[0]['n'];
			
			return ceil($length / $count);
		}
	}
?>