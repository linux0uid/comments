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
$validates = Comment::validateMore($arr, $db);

if($validates) {
	
    if(Comment::is_admin_uuid($arr['uuid'], $db)) {
        $query = "  SELECT
                        `id`, `name`, `body`, `email`, `uuid`, `public`, `date`, `ip`
                    FROM `". DB_TABLE ."`
                    WHERE `url`='". $arr['url'] ."'
                    ORDER BY id ASC
                    LIMIT ". $arr['startFrom'] .", ". AJAX_QUANTITY;
    } else {
        $query = "  SELECT
                        `id`, `name`, `body`, `email`, `uuid`, `public`, `date`, `ip`
                    FROM `". DB_TABLE ."`
                    WHERE `url`='". $arr['url'] ."'
                        AND (`public` OR `uuid`=UNHEX('". $arr['uuid'] ."'))
                    ORDER BY id ASC
                    LIMIT ". $arr['startFrom'] .", ". AJAX_QUANTITY;
    }

    $result = $db->query($query);

    while($row = mysqli_fetch_assoc($result)) {
        $row['uuid'] = bin2hex($row['uuid']);
    	$comments[] = new Comment($row, $arr['uuid']);
    }
    $result->free();

    $insertedComments = '';
    foreach($comments as $c){
    	$insertedComments .= $c->markup($db);
    }
    unset($mysql);
	/* Вывод разметки только-что вставленного комментария: */

    $status = $insertedComments == '' ? 0 : 1;
	echo json_encode(array('status'=>$status, 'html'=>$insertedComments));

} else {
	/* Вывод сообщений об ошибке */
	echo '{"status":0,"errors":'.json_encode($arr).'}';
}

