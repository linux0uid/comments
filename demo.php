<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Система комментариев для сайта на PHP | Демонстрация для сайта RUDEBOX</title>

<link rel="stylesheet" type="text/css" href="styles.css" />

</head>

<body>

<br><br><br><br><br><br><br><br><br><br><br>
<div id="main">

<?php require implode(DIRECTORY_SEPARATOR, array($_SERVER['DOCUMENT_ROOT'], "comments", "controller.php")); ?>


</div>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="script.js"></script>

</body>
</html>
