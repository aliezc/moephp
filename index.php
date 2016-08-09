<?php
	// pdo connect string
	define('DBCONN', 'mysql:host=127.0.0.1;dbname=test');
	
	// pdo user name
	define('DBUSER', 'root');
	
	// pdo user password
	define('DBPWD', '123');
	
	// pdo charactor use utf8
	define('DBUTF8', true);
	
	// url mode, 0 = PATH_INFO, 1 = rewirte, 2 = raw
	define('URLMODE', 0);
	
	// app path
	define('APPPATH', 'app');
	
	// 3rd part lib
	define('LIBPATH', 'lib');
	
	// hostname
	define('HOST', 'localhost');
	
	// index file
	define('INDEX', 'index.php');
	
	// scheme
	define('SCHEME', 'http:');
	
	// public dir
	define('PUBLIC', 'public');
	
	require_once "moe/moe.php";
	
	MOE::init();
?>