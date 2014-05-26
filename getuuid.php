<?php
define (PATH_ROOT, dirname(__FILE__));

// Сообщение об ошибке:
error_reporting(E_ALL^E_NOTICE);

include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php';
//include_once implode(DIRECTORY_SEPARATOR, array(PATH_ROOT, "include", "comment.class.php"));
include_once implode(DIRECTORY_SEPARATOR, array(PATH_ROOT, "include", "mysql.class.php"));

/*
/	Данный массив будет наполняться либо данными,
/	которые передаются в скрипт,
/	либо сообщениями об ошибке.
/*/

$mysql = new MySQL;
$db = $mysql->db;

	
$result = $db->query("SELECT UUID();");
$uuid = $result->fetch_array();
$uuid = $uuid[0];
$uuid_hash = md5($uuid . UUID_SOLL);

echo json_encode(array(
    'uuid'      =>  $uuid,
    'uuidHash'  =>  $uuid_hash,
));

