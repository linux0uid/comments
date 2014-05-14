<?php

/* Конфигурация базы данных */

$db_host		= 'localhost';
$db_user		= 'comments.dev';
$db_pass		= 'Rydn9VPFeJMGGxwa';
$db_database	= 'comments.dev'; 

/* Конец секции */


$link = @mysql_connect($db_host,$db_user,$db_pass) or die('Не могу установить соединение с базой данных');

mysql_query("SET NAMES 'utf8'");
mysql_select_db($db_database,$link);

?>
