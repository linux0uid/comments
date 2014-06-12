<?php

/*
/	Вывод комментариев один за другим:
*/

$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$hash = md5($url . URL_SOLL);
?>
<?php $exit =  '';
?>

<script type="text/javascript">

jQuery(".CommentContainer #addCommentContainer input#submit").after(function(){
    var result = '<a href="<?php echo "http://" . $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR . "/exit.php"; ?>" class="exit" title="Выйти из админки">'+
                    '<span>Выйти</span>'+
                '</a>';
    return result;
});
var publicInProgress = false;

function public_comment(commentID) {

    var controllButton = "#comment-" +commentID +" .controll-button";

    jQuery.ajax({
        /* адрес файла-обработчика запроса */
        url: '<?php echo "http://" . $_SERVER['SERVER_NAME'] . "/" . ROOT_DIR . "/public.php"; ?>',
        /* метод отправки данных */
        method: 'POST',
        /* данные, которые мы передаем в файл-обработчик */
        data: {
            "commentID" : commentID,
            "url"       : "<?php echo $url; ?>",
            "hash"      : "<?php echo $hash; ?>"
        },
        /* что нужно сделать до отправки запрса */
        beforeSend: function() {
            /* меняем значение флага на true, т.е. запрос сейчас в процессе выполнения */
            publicInProgress = true;
        }
        /* что нужно сделать по факту выполнения запроса */
    }).done(function(data){

        /* Преобразуем результат, пришедший от обработчика - преобразуем json-строку обратно в массив */
        data = jQuery.parseJSON(data);

        var comment = "#comment-" +commentID;
	    if(data.status){

            jQuery(controllButton +" .public").remove();
            jQuery(controllButton +" .edit").before(function(){
                var buttonCancel = '<button class="unpublic" onclick="unpublic_comment(\'' +commentID +'\')" >' +
                                        '<img style="padding: 0 4px 0 0;" src="http://<?php echo $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR; ?>/img/unpublic.png" />' +
                                        '<span>Скрыть</span>' +
                                   '</button>';
                return buttonCancel;
            });

        } else {
			jQuery.each(data.errors,function(k,v){
				jQuery(comment).append('<span class="error">'+v+'</span></br>');
			});
        }

        /* По факту окончания запроса снова меняем значение флага на false */
        publicInProgress = false;
    });
}

function unpublic_comment(commentID) {

    var controllButton = "#comment-" +commentID +" .controll-button";

    jQuery.ajax({
        /* адрес файла-обработчика запроса */
        url: '<?php echo "http://" . $_SERVER['SERVER_NAME'] . "/" . ROOT_DIR . "/unpublic.php"; ?>',
        /* метод отправки данных */
        method: 'POST',
        /* данные, которые мы передаем в файл-обработчик */
        data: {
            "commentID" : commentID,
            "url"       : "<?php echo $url; ?>",
            "hash"      : "<?php echo $hash; ?>"
        },
        /* что нужно сделать до отправки запрса */
        beforeSend: function() {
            /* меняем значение флага на true, т.е. запрос сейчас в процессе выполнения */
            publicInProgress = true;
        }
        /* что нужно сделать по факту выполнения запроса */
    }).done(function(data){

        /* Преобразуем результат, пришедший от обработчика - преобразуем json-строку обратно в массив */
        data = jQuery.parseJSON(data);

        var comment = "#comment-" +commentID;
	    if(data.status){

            jQuery(controllButton +" .unpublic").remove();
            jQuery(controllButton +" .edit").before(function(){
                var buttonCancel = '<button class="public" onclick="public_comment(\'' +commentID +'\')" >' +
                                        '<img style="padding: 0 4px 0 0;" src="http://<?php echo $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR; ?>/img/public.png" />' +
                                        '<span>Публиковать</span>' +
                                   '</button>';
                return buttonCancel;
            });

        } else {
			jQuery.each(data.errors,function(k,v){
				jQuery(comment).append('<span class="error">'+v+'</span></br>');
			});
        }

        /* По факту окончания запроса снова меняем значение флага на false */
        publicInProgress = false;
    });
}
</script>
