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

if(Comment::is_admin()) {

    Comment::setOption('admin', $data['uuid'], $db);
    //
    // удаление GET-параметров из URL
    $url = $_SERVER['REQUEST_URI'];
    $url = 'http://' . $_SERVER['SERVER_NAME'] . $url; 
    $url = preg_replace('/^([^?]+)(\?.*?)?(#.*)?$/', '$1$3', $url);

    header("Location: ". $url);
}


// Вывод шаблона
require_once implode(DIRECTORY_SEPARATOR, array(PATH_ROOT, "views", "comments.php"));

if(Comment::is_admin_uuid($data['uuid'], $db))
    require_once implode(DIRECTORY_SEPARATOR, array(PATH_ROOT, "views", "admin_comments.php"));


unset($mysql);
