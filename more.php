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
$validates = Comment::validateMore($arr, $db);

if($validates) {
	/* Все в порядке, вставляем данные в базу: */
	
    $result = $db->query("SELECT `name`, `body`, `email`, `date` FROM ". DB_TABLE ." WHERE `url`='". $arr['url'] ."' AND (`public` OR `uuid`=UNHEX('". $arr['uuid'] ."')) ORDER BY id ASC LIMIT ". $arr['startFrom'] .", ". AJAX_QUANTITY);

	/*
	/	Данные в $arr подготовлены для запроса mysql,
	/	но нам нужно делать вывод на экран, поэтому 
	/	готовим все элементы в массиве:
	/*/
	
	$arr = array_map('stripslashes',$arr);
	
    while($row = mysqli_fetch_assoc($result)) {
    	$comments[] = new Comment($row);
    }
    $result->free();
    unset($mysql);

    $insertedComments = '';
    foreach($comments as $c){
    	$insertedComments .= $c->markup();
    }
	/* Вывод разметки только-что вставленного комментария: */

    $status = $insertedComments == '' ? 0 : 1;
	echo json_encode(array('status'=>$status, 'html'=>$insertedComments));

} else {
	/* Вывод сообщений об ошибке */
	echo '{"status":0,"errors":'.json_encode($arr).'}';
}

