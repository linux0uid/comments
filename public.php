<?php

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
$validates = Comment::validateAction($arr, $db);

if($validates) {

	/* Все в порядке, вставляем данные в базу: */

    if(Comment::is_admin_uuid($arr['uuid'], $db)) {
        $result = $db->query("  UPDATE `". DB_TABLE ."`
                                SET `public`=true
                                WHERE `id`='". $arr['commentID'] ."'
                                LIMIT 1;
                            ");
	    echo json_encode(array('status'=>1));
    } else {
	    echo '{"status":0,"errors":'.json_encode(array("error"=>"У Вас нет прав на это!")).'}';
    }

    unset($mysql);

} else {
	/* Вывод сообщений об ошибке */
	echo '{"status":0,"errors":'.json_encode($arr).'}';
}

