<?php

/*
/	Вывод комментариев один за другим:
*/

foreach($comments as $c){
	echo $c->markup();
}
?>

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
