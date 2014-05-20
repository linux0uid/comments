<?php
define (PATH_ROOT, dirname(__FILE__));

// Сообщение об ошибке:
error_reporting(E_ALL^E_NOTICE);

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
	
	$db->query("	INSERT INTO comments(name,url,email,body)
					VALUES (
						'".$arr['name']."',
						'".$arr['url']."',
						'".$arr['email']."',
						'".$arr['body']."'
					)");
    unset($mysql);

	$arr['dt'] = date('r',time());
	$arr['id'] = mysqli_insert_id();
	
	/*
	/	Данные в $arr подготовлены для запроса mysql,
	/	но нам нужно делать вывод на экран, поэтому 
	/	готовим все элементы в массиве:
	/*/
	
	$arr = array_map('stripslashes',$arr);
	
	$insertedComment = new Comment($arr);

	/* Вывод разметки только-что вставленного комментария: */

	echo json_encode(array('status'=>1,'html'=>$insertedComment->markup()));

}
else
{
	/* Вывод сообщений об ошибке */
	echo '{"status":0,"errors":'.json_encode($arr).'}';
}

