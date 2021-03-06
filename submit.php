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
$validates = Comment::validate($arr, $db);

if($validates)
{
	/* Все в порядке, вставляем данные в базу: */
	
	$db->query("	INSERT INTO `". DB_TABLE ."`(name,url,email,body,uuid,ip)
					VALUES (
						'".$arr['name']."',
						'".$arr['url']."',
						'".$arr['email']."',
						'".$arr['body']."',
						UNHEX('".$arr['uuid']."'),
						'".$arr['ip']."'
					)");

	$arr['date'] = date('r',time());
	$arr['id'] = $db->insert_id;
	
	/*
	/	Данные в $arr подготовлены для запроса mysql,
	/	но нам нужно делать вывод на экран, поэтому 
	/	готовим все элементы в массиве:
	/*/
	
	$arr = array_map('stripslashes',$arr);
	
	$insertedComment = new Comment($arr, $arr['uuid']);

	/* Вывод разметки только-что вставленного комментария: */

	echo json_encode(array('status'=>1,'html'=>$insertedComment->markup($db)));

    unset($mysql);

}
else
{
	/* Вывод сообщений об ошибке */
	echo '{"status":0,"errors":'.json_encode($arr).'}';
}

