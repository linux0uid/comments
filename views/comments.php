<?php

/*
/	Вывод комментариев один за другим:
*/

foreach($comments as $c){
	echo $c->markup();
}
?>

    <link rel="stylesheet" type="text/css" href="<?php echo "http://" . $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR . "/styles.css"; ?>" />

<div id="addCommentContainer">
	<p>Добавить комментарий</p>
	<form id="addCommentForm" method="post" action="">
    	<div>
        	<label for="name">Имя</label>
        	<input type="text" name="name" id="name" />
            
            <label for="email">Email</label>
            <input type="text" name="email" id="email" />
            
            <label for="url">Вебсайт (не обязательно)</label>
            <input type="text" name="url" id="url" />
            
            <label for="body">Содержание комментария</label>
            <textarea name="body" id="body" cols="20" rows="5"></textarea>
            
            <input type="submit" id="submit" value="Отправить" />
        </div>
    </form>
</div>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<!-- <script type="text/javascript" src="<?php //echo implode(DIRECTORY_SEPARATOR, array("comments", "script.js")); ?>"></script> --!>
<script type="text/javascript">
$(document).ready(function(){
	/* Следующий код выполняется только после загрузки DOM */
	
	/* Данный флаг предотвращает отправку нескольких комментариев: */
	var working = false;
	
	/* Ловим событие отправки формы: */
	$('#addCommentForm').submit(function(e){

 		e.preventDefault();
		if(working) return false;
		
		working = true;
		$('#submit').val('Занято...');
		$('span.error').remove();
		
		/* Отправляем поля формы в submit.php: */
        $.post('<?php echo "http://" . $_SERVER['SERVER_NAME'] . "/" . ROOT_DIR . "/submit.php"; ?>',$(this).serialize(),function(msg){

			working = false;
			$('#submit').val('Отправить');
			
			if(msg.status){

				/* 
				/	Если вставка была успешной, добавляем комментарий 
				/	ниже последнего на странице с эффектом slideDown
				/*/

				$(msg.html).hide().insertBefore('#addCommentContainer').slideDown();
				$('#body').val('');
			}
			else {

				/*
				/	Если есть ошибки, проходим циклом по объекту
				/	msg.errors и выводим их на страницу
				/*/
				
				$.each(msg.errors,function(k,v){
					$('label[for='+k+']').append('<span class="error">'+v+'</span>');
				});
			}
		},'json');

	});
	
});
</script>
