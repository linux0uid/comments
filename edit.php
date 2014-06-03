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
$validates = Comment::validateEdit($arr, $db);

if($validates) {
	/* Все в порядке, вставляем данные в базу: */
	
    $result = $db->query("  SELECT
                                `id`,
                                `name`,
                                `email`
                            FROM `". DB_TABLE ."`
                            WHERE
                                `id`    = '".$arr['commentID']."'
                            AND
					        	`uuid`  = UNHEX('".$arr['uuid']."')
                            AND NOT `public`
                            LIMIT 1;
    ");
    if($row = mysqli_fetch_assoc($result)) {

        $arr += $row;
        $result->free();

        $db->query("	UPDATE `". DB_TABLE ."`
                        SET
                            `body`  = '".$arr['body']."',
	    					`ip`    = '".$arr['ip']."',
                            `date`  = now()
                        WHERE
                            `id`    = '".$arr['commentID']."'
                        AND
	    					`uuid`  = UNHEX('".$arr['uuid']."')
                        LIMIT 1;
	    ");

	    $arr['date'] = date('r',time());
        
	    
	    /*
	    /	Данные в $arr подготовлены для запроса mysql,
	    /	но нам нужно делать вывод на экран, поэтому 
	    /	готовим все элементы в массиве:
	    /*/
	    
	    $arr = array_map('stripslashes',$arr);
	    
	    $insertedComment = new Comment($arr);

	    /* Вывод разметки только-что вставленного комментария: */

	    echo json_encode(array('status'=>0,'html'=>$insertedComment->markup($db)));

    } else {
	    echo '{"status":1,"errors":'.json_encode(array("error"=>"Вы уже не можете редактировать этот комментарий!")).'}';
    }

} else {
	/* Вывод сообщений об ошибке */
	echo '{"status":2,"errors":'.json_encode($arr).'}';
}

unset($mysql);
