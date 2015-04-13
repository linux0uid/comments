<?php

/**
 * Class: MySQL
 *
 */
class MySQL
{
    /**
     * db
     *
     * @var mixed
     */
    public $db;

    /**
     * __construct
     *
     */
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

    /**
     * __destruct
     *
     */
    public function __destruct() {
        $this->db->close();
    }

}
