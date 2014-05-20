<?php
define (PATH_ROOT, dirname(__FILE__));


// Сообщение об ошибке:
error_reporting(E_ALL^E_NOTICE);

include_once implode(DIRECTORY_SEPARATOR, array(PATH_ROOT, "include", "comment.class.php"));
include_once implode(DIRECTORY_SEPARATOR, array(PATH_ROOT, "include", "mysql.class.php"));


/*
/	Выбираем все комментарии и наполняем массив $comments объектами
*/
//global Comment::db;
$comments = array();

$mysql = new MySQL;
$db = $mysql->db;

$result = $db->query("SELECT * FROM comments ORDER BY id ASC");

while($row = mysqli_fetch_assoc($result))
{
	$comments[] = new Comment($row);
}
$result->free();

unset($mysql);
//MySQL::close();

require "views/comments.php";
