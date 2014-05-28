<?php
define (ROOT_DIR, substr(dirname(__FILE__), strlen(realpath($_SERVER['DOCUMENT_ROOT']))+1));

// Сообщение об ошибке:
error_reporting(E_ALL^E_NOTICE);

include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php';


require_once implode(DIRECTORY_SEPARATOR, array(PATH_ROOT, "views", "comments.php"));
