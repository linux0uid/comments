<?php

include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php';
include_once implode(DIRECTORY_SEPARATOR, array(PATH_ROOT, "include", "mysql.class.php"));

$mysql = new MySQL;
$db = $mysql->db;

$query =  " CREATE TABLE IF NOT EXISTS `". DB_TABLE ."` (
              `id` int(10) unsigned NOT NULL auto_increment,
              `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
              `name` varchar(128) collate utf8_unicode_ci NOT NULL default '',
              `url` varchar(255) collate utf8_unicode_ci NOT NULL default '',
              `email` varchar(255) collate utf8_unicode_ci NOT NULL default '',
              `body` text collate utf8_unicode_ci NOT NULL,
              `public` BOOLEAN NOT NULL DEFAULT FALSE,
              `ip` char(15) NOT NULL default '',
              `uuid` binary(16) NOT NULL,
              PRIMARY KEY  (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

            CREATE TABLE IF NOT EXISTS `". DB_TABLE_STOP_WORDS ."` (
              `id` int(10) unsigned NOT NULL auto_increment,
              `name` varchar(128) collate utf8_unicode_ci NOT NULL default '',
              PRIMARY KEY  (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            INSERT INTO `". DB_TABLE_STOP_WORDS ."`
                SET `name`='админ';
            INSERT INTO `". DB_TABLE_STOP_WORDS ."`
                SET `name`='консультант';

            CREATE TABLE IF NOT EXISTS `". DB_TABLE_OPTIONS ."` (
              `id` int(10) unsigned NOT NULL auto_increment,
              `name` varchar(128) collate utf8_unicode_ci NOT NULL default '',
              `value` varchar(128) collate utf8_unicode_ci NOT NULL default '',
              PRIMARY KEY  (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            INSERT INTO `". DB_TABLE_OPTIONS ."`
                SET `name`='admin',
                    `value`='';
";

if($db->multi_query($query)) {
    echo 'Скрипт успешно установлен, можете удалить install.php';
} else {
    echo 'Установка НЕ удалась! Проверьте конфигурационный файл.';
}
