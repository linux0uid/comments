<?php

/*
/	Вывод комментариев один за другим:
*/

$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$hash = md5($url . URL_SOLL);
?>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.js"></script>
<script src="<?php echo 'http://' . $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR; ?>/ckeditor/ckeditor.js"></script>
<script src="<?php echo 'http://' . $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR; ?>/ckeditor/adapters/jquery.js"></script>
<script type="text/javascript">
jQuery( document ).ready( function() {
	jQuery('textarea#commentsBody').ckeditor({
	allowedContent:
		'p strong em;' +
		'a[!href,!target];' +
		'span{!color};'
} );
} );
</script>
<link rel="stylesheet" type="text/css" href="<?php echo "http://" . $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR . "/styles.css"; ?>" />

<div class="CommentContainer">
<div id="addCommentContainer">
	<p>Добавить комментарий</p>
	<form id="addCommentForm" method="post" action="">
    	<div>
        	<label for="name">Имя</label>
        	<input type="text" name="name" id="name" />
            
            <label for="email">Email</label>
            <input type="hidden" name="email" id="email" />
            <input type="text" name="mail" id="email" />
            
            <label for="body">Содержание комментария</label>
            <textarea name="body" id="commentsBody" cols="20" rows="5"></textarea>
            
            <input type="hidden" name="url" value="<?php echo $url; ?>" />
            <input type="hidden" name="hash" value="<?php echo $hash; ?>" />
            <input type="submit" id="submit" value="Отправить" />
        </div>
    </form>
</div>
</div>

<script type="text/javascript" src="<?php echo "http://" . $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR . "/evercookie/evercookie.js"; ?>"></script>
<script type="text/javascript" src="<?php echo "http://" . $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR . "/evercookie/swfobject.js"; ?>"></script>
<script type="text/javascript">
    //
    // retrieve a cookie called "id" (simply)
var cookID;

jQuery(document).ready(function($){
	/* Следующий код выполняется только после загрузки DOM */

	var ec = new evercookie();
    
    // set a cookie "id" to "12345"
    // usage: ec.set(key, value)

    /* ajax подгрузка комментариев */
    //var comment_id = 1;
    var moreComments = true;


    /* Переменная-флаг для отслеживания того, происходит ли в данный момент ajax-запрос. В самом начале даем ей значение false, т.е. запрос не в процессе выполнения */
    var inProgress = false;
    /* С какой статьи надо делать выборку из базы при ajax-запросе */
    var startFrom = 0;
    //
    jQuery('#commentsBody').val('');


    ec.get("id", function(cookID) {
        function getComment() {

            jQuery.ajax({
                /* адрес файла-обработчика запроса */
                url: '<?php echo "http://" . $_SERVER['SERVER_NAME'] . "/" . ROOT_DIR . "/more.php"; ?>',
                /* метод отправки данных */
                method: 'POST',
                /* данные, которые мы передаем в файл-обработчик */
                data: {
                    "startFrom" : startFrom,
                    "id"        : cookID,
                    "url"       : "<?php echo $url; ?>",
                    "hash"      : "<?php echo $hash; ?>"
                },
                /* что нужно сделать до отправки запрса */
                beforeSend: function() {
                    /* меняем значение флага на true, т.е. запрос сейчас в процессе выполнения */
                    inProgress = true;
                }
                /* что нужно сделать по факту выполнения запроса */
            }).done(function(data){
            
                /* Преобразуем результат, пришедший от обработчика - преобразуем json-строку обратно в массив */
                data = jQuery.parseJSON(data);
            
                /* Если массив не пуст (т.е. статьи там есть) */
	            if(data.status){
            		/* 
            		/	Если вставка была успешной, добавляем комментарий 
            		/	ниже последнего на странице с эффектом slideDown
            		/*/
                    data = jQuery.parseHTML(data.html);
            		jQuery(data).hide().insertBefore('#addCommentContainer').slideDown();
            
                    /* По факту окончания запроса снова меняем значение флага на false */
                    inProgress = false;
                    // Увеличиваем на 10 порядковый номер статьи, с которой надо начинать выборку из базы
                    startFrom += <?php echo AJAX_QUANTITY; ?>;
                } else {
                    moreComments = data.status;
                }
            });
        }
        //cookID = value;
        function getUuid() {

            jQuery.ajax({
                /* адрес файла-обработчика запроса */
                url: '<?php echo "http://" . $_SERVER['SERVER_NAME'] . "/" . ROOT_DIR . "/getuuid.php"; ?>',
                /* метод отправки данных */
                method: 'POST',
                /* данные, которые мы передаем в файл-обработчик */
                data: {
                    "url"       : "<?php echo $url; ?>"
                },
                /* что нужно сделать до отправки запрса */
                beforeSend: function() {
                    /* меняем значение флага на true, т.е. запрос сейчас в процессе выполнения */
                    inProgress = true;
                }
                /* что нужно сделать по факту выполнения запроса */
            }).done(function(data){
            
                /* Преобразуем результат, пришедший от обработчика - преобразуем json-строку обратно в массив */
                data = jQuery.parseJSON(data);
                cookID = data.uuid + '$' + data.uuidHash;
            
                ec.set("id", cookID);
                        /* По факту окончания запроса снова меняем значение флага на false */
                inProgress = false;
                getComment();
            });
        }
        if(cookID === 'null') {
            getUuid();
        } else {
            getComment();
        }
        
        jQuery(window).scroll(function() {
            /* Если высота окна + высота прокрутки больше или равны высоте всего документа и ajax-запрос в настоящий момент не выполняется, то запускаем ajax-запрос */
            if(jQuery(window).scrollTop() + jQuery(window).height() >= jQuery(document).height() - 800 && !inProgress && moreComments) {
                getComment();
            }
        });
    });


	/* Данный флаг предотвращает отправку нескольких комментариев: */
	var working = false;
	
	/* Ловим событие отправки формы: */
	jQuery('#addCommentForm').submit(function(e){

 		e.preventDefault();
		if(working) return false;
		
		working = true;
		jQuery('#addCommentForm #submit').val('Занято...');
		jQuery('#addCommentForm span.error').remove();
		
		/* Отправляем поля формы в submit.php: */
        jQuery.post('<?php echo "http://" . $_SERVER['SERVER_NAME'] . "/" . ROOT_DIR . "/submit.php"; ?>',$(this).serialize(),function(msg){

			working = false;
			jQuery('#submit').val('Отправить');
			
			if(msg.status){

				/* 
				/	Если вставка была успешной, добавляем комментарий 
				/	ниже последнего на странице с эффектом slideDown
				/*/

                data = jQuery.parseHTML(msg.html);
				jQuery(data).hide().insertBefore('#addCommentContainer').slideDown();
				jQuery('#commentsBody').val('');
			}
			else {

				/*
				/	Если есть ошибки, проходим циклом по объекту
				/	msg.errors и выводим их на страницу
				/*/
				
				jQuery.each(msg.errors,function(k,v){
					jQuery('label[for='+k+']').append('<span class="error">'+v+'</span>');
				});
			}
		},'json');

	});
	
});
</script>

<script type="text/javascript">

var saveInProgress = false;
var deleteInProgress = false;

function edit_comment(commentID) {
    var commentSrc = "#comment-" +commentID +" .content";
    var oldComment = jQuery(commentSrc).html();
    jQuery(commentSrc).empty();
    jQuery(commentSrc).append('<textarea></textarea>');
    commentSrc += ' textarea';
    jQuery(commentSrc).append(oldComment);
    jQuery(commentSrc).ckeditor({
	    allowedContent:
	    	'p strong em;' +
	    	'a[!href];' +
	    	'span{!color};'
    });
    jQuery(commentSrc).val(oldComment);
    var controllButton = "#comment-" +commentID +" .controll-button";
    jQuery(controllButton +" .edit").remove();
    jQuery(controllButton +" .delete").before(function(){
        var buttonCancel =  '<button class="save" onclick="save_comment(\'' +commentID +'\')" >' +
                                '<img style="padding: 0 2px;" src="http://<?php echo $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR; ?>/img/save.png" />' +
                                '<span>Сохранить</span>' +
                            '</button>';
        buttonCancel +=     '<button class="cancel" onclick="cancel_comment(\'' +commentID +'\')" >' +
                                '<img style="padding: 0 2px;" src="http://<?php echo $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR; ?>/img/cancel.png" />' +
                                '<span>Отменить</span>' +
                           '</button>';
        return buttonCancel;
    });
}

function save_comment(commentID){
    if(saveInProgress){
        return;
    }

    var commentSrc = "#comment-" +commentID;
	jQuery(commentSrc +' span.error').remove();
    var commentSrcTextarea = commentSrc +" .content textarea";
    var newBody = jQuery(commentSrcTextarea).val();
    jQuery.ajax({
        /* адрес файла-обработчика запроса */
        url: '<?php echo "http://" . $_SERVER['SERVER_NAME'] . "/" . ROOT_DIR . "/edit.php"; ?>',
        /* метод отправки данных */
        method: 'POST',
        /* данные, которые мы передаем в файл-обработчик */
        data: {
            "commentID" : commentID,
            "body"      : newBody,
            "url"       : "<?php echo $url; ?>",
            "hash"      : "<?php echo $hash; ?>"
        },
        /* что нужно сделать до отправки запрса */
        beforeSend: function() {
            /* меняем значение флага на true, т.е. запрос сейчас в процессе выполнения */
            saveInProgress = true;
        }
        /* что нужно сделать по факту выполнения запроса */
    }).done(function(data){

        /* Преобразуем результат, пришедший от обработчика - преобразуем json-строку обратно в массив */
        data = jQuery.parseJSON(data);

        var comment = "#comment-" +commentID;
	    switch(data.status){
        case 0:
			/* 
			/	Если удаление было успешным, удаляем комментарий 
			/	на странице с эффектом slideUp
			/*/
            var nextComment = jQuery(commentSrc).next();
            jQuery(commentSrc).remove();
            var newComment = jQuery.parseHTML(data.html);
            jQuery(nextComment).before(newComment);
            break
        case 1:
            cancel_comment(commentID);
            jQuery(commentSrc + ' .controll-button').remove();
        case 2:
			jQuery.each(data.errors,function(k,v){
				jQuery(commentSrc).append('<span class="error">'+v+'</span>');
			});
            break
        }

        /* По факту окончания запроса снова меняем значение флага на false */
        saveInProgress = false;
    });
}

function cancel_comment(commentID){
    var commentSrc = "#comment-" +commentID;
	jQuery(commentSrc +' span.error').remove();
    commentSrc +=  " .content";
    var commentSrcTextarea = commentSrc +" textarea";
    var oldComment = jQuery(commentSrcTextarea).html();
    jQuery(commentSrc).empty().append(oldComment);

    var controllButton = "#comment-" +commentID +" .controll-button";
    jQuery(controllButton +" .save").remove();
    jQuery(controllButton +" .cancel").remove();
    jQuery(controllButton +" .delete").before(function(){
        var buttonCancel = '<button class="edit" onclick="edit_comment(\'' +commentID +'\')" >' +
                                '<img style="padding: 0 2px;" src="http://<?php echo $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR; ?>/img/edit.png" />' +
                                '<span>Редактировать</span>' +
                           '</button>';
        return buttonCancel;
    });
}

function delete_comment(commentID) {

    jQuery.ajax({
        /* адрес файла-обработчика запроса */
        url: '<?php echo "http://" . $_SERVER['SERVER_NAME'] . "/" . ROOT_DIR . "/delete.php"; ?>',
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
            deleteInProgress = true;
        }
        /* что нужно сделать по факту выполнения запроса */
    }).done(function(data){

        /* Преобразуем результат, пришедший от обработчика - преобразуем json-строку обратно в массив */
        data = jQuery.parseJSON(data);

        var comment = "#comment-" +commentID;
	    if(data.status){
			/* 
			/	Если удаление было успешным, удаляем комментарий 
			/	на странице с эффектом slideUp
			/*/
            jQuery(comment).slideUp('slow').fadeOut('slow', function() {
                this.remove();
            });

        } else {
			jQuery.each(data.errors,function(k,v){
				jQuery(comment).append('<span class="error">'+v+'</span></br>');
                jQuery(comment + ' .controll-button').remove();
			});
        }

        /* По факту окончания запроса снова меняем значение флага на false */
        deleteInProgress = false;
    });
}

</script>
