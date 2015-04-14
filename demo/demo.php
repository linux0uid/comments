<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php include_once dirname(__FILE__) . '/../config.php'; ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
<title>Система комментариев для сайта на PHP</title>

<link rel="stylesheet" type="text/css" href="<?php echo "http://" . $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR . "/demo/demo.css"; ?>" />

</head>

<body>

<br><br><br><br><br><br><br><br><br><br><br>
<div id="main">

<?php require implode(DIRECTORY_SEPARATOR, array($_SERVER['DOCUMENT_ROOT'], "comments", "controller.php")); ?>


</div>


</body>
</html>
