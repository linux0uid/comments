<?php

// Сообщение об ошибке:
error_reporting(E_ALL^E_NOTICE);

include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php';
include_once implode(DIRECTORY_SEPARATOR, array(PATH_ROOT, "include", "comment.class.php"));
include_once implode(DIRECTORY_SEPARATOR, array(PATH_ROOT, "include", "mysql.class.php"));

$mysql = new MySQL;
$db = $mysql->db;

$errors = array();
$data	= array();

Comment::validateUuid($data, $errors);

if(Comment::is_admin_uuid($data['uuid'], $db)) {

    Comment::setOption('admin', '', $db);
    //
    // удаление GET-параметров из URL
    $url = $_SERVER['HTTP_REFERER'];

    echo '<meta http-equiv="refresh" content="0; url='.$url.'">';
}

unset($mysql);
