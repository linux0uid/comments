<?php

/*
/	Вывод комментариев один за другим:
*/

/*foreach($comments as $c){*/
	//echo $c->markup();
/*}*/

$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$hash = md5($url . URL_SOLL);
?>

    <link rel="stylesheet" type="text/css" href="<?php echo "http://" . $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR . "/styles.css"; ?>" />

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
            <textarea name="body" id="body" cols="20" rows="5"></textarea>
            
            <input type="hidden" name="url" value="<?php echo $url; ?>" />
            <input type="hidden" name="hash" value="<?php echo $hash; ?>" />
            <input type="submit" id="submit" value="Отправить" />
        </div>
    </form>
</div>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.js"></script>
<script type="text/javascript" src="<?php echo "http://" . $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR . "/evercookie/evercookie.js"; ?>"></script>
<script type="text/javascript">
jQuery(document).ready(function($){
	/* Следующий код выполняется только после загрузки DOM */

	var ec = new evercookie();
    
    // set a cookie "id" to "12345"
    // usage: ec.set(key, value)
<?php
    //if(!(isset($_COOKIE['id'])) || (is_null($_COOKIE['id'])) || empty($_COOKIE['id']) || ($_COOKIE['id'] == 'null')) {
        //echo '    ec.set("id", "123456")';
    //}
?>
    

    /* ajax подгрузка комментариев */
    //var comment_id = 1;
    var moreComments = true;


    /* Переменная-флаг для отслеживания того, происходит ли в данный момент ajax-запрос. В самом начале даем ей значение false, т.е. запрос не в процессе выполнения */
    var inProgress = false;
    /* С какой статьи надо делать выборку из базы при ajax-запросе */
    var startFrom = 0;
    //
    //
    // retrieve a cookie called "id" (simply)
    var cookID;

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
        
                    /* Если массив не пуст (т.е. статьи там есть) */
	    		    if(data.status){
        				/* 
        				/	Если вставка была успешной, добавляем комментарий 
        				/	ниже последнего на странице с эффектом slideDown
        				/*/
                        data = jQuery.parseHTML(data.html);
        				jQuery(data).hide().insertBefore('#addCommentContainer').slideDown();
                        //jQuery('#body').val('');
        
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
        
        /* Используйте вариант $('#more').click(function() для того, чтобы дать пользователю возможность управлять процессом, кликая по кнопке "Дальше" под блоком статей (см. файл index.php) */
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
		jQuery('#submit').val('Занято...');
		jQuery('span.error').remove();
		
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
				jQuery('#body').val('');
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
