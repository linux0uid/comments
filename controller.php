<?php
define (PATH_ROOT, __DIR__);
//define (ROOT_DIR, substr(strrchr(dirname(__FILE__), '/'), 1));
define (ROOT_DIR, substr(dirname(__FILE__), strlen(realpath($_SERVER['DOCUMENT_ROOT']))+1));

// Сообщение об ошибке:
error_reporting(E_ALL^E_NOTICE);

include_once PATH_ROOT . DIRECTORY_SEPARATOR . 'config.php';
include_once implode(DIRECTORY_SEPARATOR, array(PATH_ROOT, "include", "comment.class.php"));
include_once implode(DIRECTORY_SEPARATOR, array(PATH_ROOT, "include", "mysql.class.php"));


/*
/	Выбираем все комментарии и наполняем массив $comments объектами
*/
//global Comment::db;
$comments = array();

$mysql = new MySQL;
$db = $mysql->db;

$result = $db->query("SELECT * FROM ". $db_table ." ORDER BY id ASC");

while($row = mysqli_fetch_assoc($result))
{
	$comments[] = new Comment($row);
}
$result->free();

unset($mysql);
//MySQL::close();

require_once implode(DIRECTORY_SEPARATOR, array(PATH_ROOT, "views", "comments.php"));
