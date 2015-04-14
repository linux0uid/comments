==Система комментирования для сайта


===УСТАНОВКА

1. Распакуйте содержимое архива в корневую папку на сайт.
2. В файле config.php настройте доступ к вашей базе данных.
3. Запустите в браузере файл http://{ваш_сайт}/comments/install.php
4. Встройте в страницу своего сайта, в том месте где необходимо отображение комментариев, следующий код:

	<?php require implode(DIRECTORY_SEPARATOR, array($_SERVER['DOCUMENT_ROOT'], "comments", "controller.php")); ?>

5. Для редактирования комментариев необходимо зайти с на любую страницу сайта, где подключена система комментирования, с ключем ?adminkey=key. (Пр. ..comments/demo/demo.php?adminkey=nABVYU2ubCdHuwNXuLYnI52iXar9lT)
6. С ключом adminkey зайти нужно только один раз. Дальше редактировать комментарии можно на любой странице сайта.
6. Для того, что бы изменить adminkey, нужно md5 функцию нового сгенерированного adminkey установить в константе ADMIN_KEY_MD5 в файле config.php.
Внимание! Не сам ключ, а его хеш md5.


===License

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
