<?php

//include_once PATH_ROOT . DIRECTORY_SEPARATOR . 'config.php';
//include_once implode(DIRECTORY_SEPARATOR, array($_SERVER['DOCUMENT_ROOT'], "comments", "config.php"));

class MySQL
{
    public $db;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (mysqli_connect_error()) {
            die('Ошибка подключения (' . mysqli_connect_errno() . ') '
                . mysqli_connect_error()
            );
        }
        $this->db->query("SET NAMES 'utf8'");
        //mysqli_select_db($db_database,$this->db);
    }

    public function __destruct() {
        $this->db->close();
    }

    //public static $db;

    //public static function open() {
        //global $db_host, $db_database, $db_user, $db_pass;
        //self::$db = new mysqli($db_host,$db_user,$db_pass,$db_database);
        //if (mysqli_connect_error()) {
            //die('Ошибка подключения (' . mysqli_connect_errno() . ') '
                //. mysqli_connect_error()
            //);
        //}
        //self::$db->query("SET NAMES 'utf8'");
        ////mysqli_select_db($db_database,$this->db);
    //}

    //public static function close() {
        //self::$db->close();
    //}

}

