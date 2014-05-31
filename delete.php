<?php
define (PATH_ROOT, dirname(__FILE__));

// Сообщение об ошибке:
error_reporting(E_ALL^E_NOTICE);

include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php';
include_once implode(DIRECTORY_SEPARATOR, array(PATH_ROOT, "include", "comment.class.php"));
include_once implode(DIRECTORY_SEPARATOR, array(PATH_ROOT, "include", "mysql.class.php"));

/*
/	Данный массив будет наполняться либо данными,
/	которые передаются в скрипт,
/	либо сообщениями об ошибке.
/*/

$mysql = new MySQL;
$db = $mysql->db;

$arr = array();
$validates = Comment::validateDelete($arr, $db);

if($validates) {
	/* Все в порядке, вставляем данные в базу: */
	
    $result = $db->query("DELETE FROM `". DB_TABLE ."` WHERE `id`='". $arr['commentID'] ."' AND `uuid`=UNHEX('". $arr['uuid'] ."') AND NOT `public` LIMIT 1;");

    if($db->affected_rows == 1) {
	    echo json_encode(array('status'=>1));
    } else {
	    echo '{"status":0,"errors":'.json_encode(array("error"=>"Вы уже не можете удалить этот комментарий!")).'}';
    }

    unset($mysql);


} else {
	/* Вывод сообщений об ошибке */
	echo '{"status":0,"errors":'.json_encode($arr).'}';
}

