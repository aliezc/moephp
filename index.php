<?php
define('MYSQL_HOST', 'localhost');
define('MYSQL_DATABASE', 'test');
define('MYSQL_USERNAME', 'root');
define('MYSQL_PASSWORD', '123');

define('PATHINFO', true);

require_once 'moe.php';

moe_route('/', 'app/home.php');
moe_route('/news/$id', 'app/news.php');
?>