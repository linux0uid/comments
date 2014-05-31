<?php

// Сообщение об ошибке:
error_reporting(E_ALL^E_NOTICE);

include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php';


require_once implode(DIRECTORY_SEPARATOR, array(PATH_ROOT, "views", "comments.php"));
