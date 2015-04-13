<?php

/* Конфигурация базы данных */

define ('DB_HOST',              'localhost');
define ('DB_USER',              'comments.dev');
define ('DB_PASS',              'Rydn9VPFeJMGGxwa');
define ('DB_NAME',              'comments.dev'); 


/* Таблицы базы данных */

define ('DB_TABLE',             'comm_comments');
define ('DB_TABLE_STOP_WORDS',  'comm_stopwords');
define ('DB_TABLE_OPTIONS',     'comm_options');

//define ('ADMIN_KEY',            'nABVYU2ubCdHuwNXuLYnI52iXar9lT');
define ('ADMIN_KEY_MD5',        '39963abc717c18f70fdb6b12d17ec55e');

/* Количество комментариев, получаемое за один раз*/

define ('AJAX_QUANTITY',  '10');


define ('PATH_ROOT', dirname(__FILE__));
define ('ROOT_DIR', substr(dirname(__FILE__), strlen(realpath($_SERVER['DOCUMENT_ROOT']))+1));


/*
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 */

define ('URL_SOLL',        'k[^W]BGiK:^x!LG_iOS337]n=mE}:2W4IQznhX|2h0 _M4Ci-{qH-6FlDd#pPoCp');
define ('USER_SOLL',       '5uH`D~_iKO4x?D`>fF$A[[NU*%e.pERPs&awA!EBJ.9-R2FEw~UI3+tKKhkC(|DX');
define ('UUID_SOLL',       '!h6{8>U${n0f`EZms+thACKVt~T~*5c>v?f1]`il`Xmo4BO2iD^h3_hM*t+73%TP');
