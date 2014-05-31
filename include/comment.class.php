<?php

class Comment
{
	private $data = array();
	
    private function rdate($format, $timestamp = null, $case = 0)
    {
        if ( $timestamp === null )
            $timestamp = time();
        
        static $loc =
            'Январ,ь,я,е,ю,ём,е
            Феврал,ь,я,е,ю,ём,е
            Март, ,а,е,у,ом,е
            Апрел,ь,я,е,ю,ем,е
            Ма,й,я,е,ю,ем,е
            Июн,ь,я,е,ю,ем,е
            Июл,ь,я,е,ю,ем,е
            Август, ,а,е,у,ом,е
            Сентябр,ь,я,е,ю,ём,е
            Октябр,ь,я,е,ю,ём,е
            Ноябр,ь,я,е,ю,ём,е
            Декабр,ь,я,е,ю,ём,е';
        
        if ( is_string($loc) )
        {
            $months = array_map('trim', explode("\n", $loc));
            $loc = array();
            foreach($months as $monthLocale)
            {
                $cases = explode(',', $monthLocale);
                $base = array_shift($cases);
                
                $cases = array_map('trim', $cases);
                
                $loc[] = array(
                    'base' => $base,
                    'cases' => $cases,
                );
            }
        }
        
        $m = (int)date('n', $timestamp)-1;
        
        $F = $loc[$m]['base'].$loc[$m]['cases'][$case];
        
        $format = strtr($format, array(
            'F' => $F,
            'M' => substr($F, 0, 3),
        ));
        
    return date($format, $timestamp);
    }

	public function __construct($row = null)
	{
		/*
		/	Конструктор
		*/

		$this->data = $row;
	}
	
	public function markup(&$db)
	{
		/*
		/	Данный метод выводит разметку XHTML для комментария
		*/
		
		// Устанавливаем псевдоним, чтобы не писать каждый раз $this->data:
		$d = &$this->data;
		
		$link_open = '';
		$link_close = '';
		
		// Преобразуем время в формат UNIX:
		$d['date'] = strtotime($d['date']);
		
		// Нужно для установки изображения по умолчанию:
		$url = 'http://'.dirname($_SERVER['SERVER_NAME'] . '/' . ROOT_DIR) . '/img/default_avatar.gif';
		
        $result = $db->query("SELECT COUNT(*) FROM `". DB_TABLE ."` WHERE `id`='". $d['id'] ."' AND `uuid`=UNHEX('". $d['uuid'] ."') AND NOT `public` LIMIT 1;");
        $result = $result->fetch_array();

        if ($result[0] == 1) {
            $controll_button = '<div class="controll-button">
                                    <button class="delete" onclick="delete_comment('. $d['id'] .')" >
                                        <img src="http://' . $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR . '/img/delete.png" />
                                        <span>Удалить</span>
                                    </button>
                                </div>';
        }

		return '

            <div class="comment" id="comment-'. $d['id'] .'">
				<div class="avatar">
					'.$link_open.'
					<img src="http://www.gravatar.com/avatar/'.md5($d['email']).'?size=50&amp;default='.urlencode($url).'" />
					'.$link_close.'
				</div>
				
				<div class="name">'.$link_open.$d['name'].$link_close.'</div>
				<div class="date" title="Добавлен '. $this->rdate('d F Y \в H:i', $d['date'], 1) .'">'. $this->rdate('d F Y', $d['date'], 1) .'</div>
                <p>'.$d['body'].'</p>'.
                $controll_button .
			'</div>
		';
	}
	
    public static function validateUuid(&$data, &$errors)
    {
        $uu = explode("$", $_COOKIE['id']);
        $uuid = $uu[0];
        $uuid_hash = $uu[1];

        if (md5($uuid . UUID_SOLL) !== $uuid_hash) {
            $errors['uuid'] = 'Не хороший ID';
        } else {
            $data['uuid'] = str_replace('-', '', $uuid);
        }
    }

    public static function validateUrl(&$data, &$errors)
    {
		if(!($data['url'] = filter_input(INPUT_POST,'url',FILTER_VALIDATE_URL))) { 
			$errors['url'] = 'Пожалуйста, введите правильный url.';
        } elseif(strpos($_POST['url'], 'http://'.$_SERVER['SERVER_NAME']) !== 0) {
			$errors['url'] = 'Пожалуйста, проверте правильность домена.';
        }
        
        if(md5($data['url']. URL_SOLL) != $_POST['hash']) {
            $errors['hash'] = 'Не верный хеш.';
        }
    }

	public static function validate(&$arr, &$db)
	{
		/*
		/	Данный метод используется для проверки данных отправляемых через AJAX.
		/
		/	Он возвращает true/false в зависимости от правильности данных, и наполняет
		/	массив $arr, который преается как параметр либо данными либо сообщением об ошибке.
		*/
		
		$errors = array();
		$data	= array();

        Comment::validateUuid($data, $errors);
        Comment::validateUrl($data, $errors);

		// Используем функцию filter_input, введенную в PHP 5.2.0

        $data['ip'] = $_SERVER['REMOTE_ADDR'];

		if(!($data['email'] = filter_input(INPUT_POST,'mail',FILTER_VALIDATE_EMAIL)))
		{
			$errors['email'] = 'Пожалуйста, введите правильный Email.';
		}
        if($_POST['email'] != '') {
            $errors['email'] = 'Хакер, что ли?';
        }
		
		// Используем фильтр с возвратной функцией:
		
		if(!($data['body'] = filter_input(INPUT_POST,'body',FILTER_CALLBACK,array('options'=>'Comment::validate_text'))))
		{
			$errors['body'] = 'Пожалуйста, введите текст комментария.';
		}
		
		if(!($data['name'] = filter_input(INPUT_POST,'name',FILTER_CALLBACK,array('options'=>'Comment::validate_text'))))
		{
			$errors['name'] = 'Пожалуйста, введите имя.';
        } elseif (mb_strlen($data['name'], 'UTF-8') < 2) {
			$errors['name'] = 'Пожалуйста, введите имя, не меньше 2 символов.';
        }
		
		if(!empty($errors)){
			
			// Если есть ошибки, копируем массив $errors в $arr:
			
			$arr = $errors;
			return false;
		}
		
		// Если данные введены правильно, подчищаем данные и копируем их в $arr:
		
		foreach($data as $k=>$v){
			$arr[$k] = mysqli_real_escape_string($db, $v);
		}
		
		// email дожен быть в нижнем регистре:
		
		$arr['email'] = strtolower(trim($arr['email']));
		
		return true;
		
	}

	public static function validateMore(&$arr, &$db)
	{
		/*
		/	Данный метод используется для проверки данных отправляемых через AJAX.
		/
		/	Он возвращает true/false в зависимости от правильности данных, и наполняет
		/	массив $arr, который преается как параметр либо данными либо сообщением об ошибке.
		*/
		
		$errors = array();
		$data	= array();
		
		// Используем функцию filter_input, введенную в PHP 5.2.0
        Comment::validateUuid($data, $errors);
        Comment::validateUrl($data, $errors);
		
        if(!($data['startFrom'] = filter_input(INPUT_POST,'startFrom',FILTER_VALIDATE_INT)))
        {
            if ($_POST['startFrom'] === '0') {
                $data['startFrom'] = '0';
            } else {
			    $errors['startFrom'] = 'Пожалуйста, введите правильный startFrom.';
            }
		}
		
		if(!empty($errors)){
			
			// Если есть ошибки, копируем массив $errors в $arr:
			
			$arr = $errors;
			return false;
		}
		
		// Если данные введены правильно, подчищаем данные и копируем их в $arr:
		
		foreach($data as $k=>$v){
			$arr[$k] = mysqli_real_escape_string($db, $v);
		}
		
		return true;
		
	}

	public static function validateDelete(&$arr, &$db)
	{
		/*
		/	Данный метод используется для проверки данных отправляемых через AJAX.
		/
		/	Он возвращает true/false в зависимости от правильности данных, и наполняет
		/	массив $arr, который преается как параметр либо данными либо сообщением об ошибке.
		*/
		
		$errors = array();
		$data	= array();
		
		// Используем функцию filter_input, введенную в PHP 5.2.0
        Comment::validateUuid($data, $errors);
        Comment::validateUrl($data, $errors);
		
        if(!($data['commentID'] = filter_input(INPUT_POST,'commentID',FILTER_VALIDATE_INT))) {
		    $errors['commentID'] = 'Пожалуйста, введите правильный commentID.';
        } else {
            $result = $db->query("SELECT COUNT(*) FROM `". DB_TABLE ."` WHERE `id`='". $data['commentID'] ."' AND `uuid`=UNHEX('". $data['uuid'] ."');");
            $result = $result->fetch_array();
            if($result[0] != 1) {
                $errors['commentID'] = 'Вам нельзя удалить этот комментарий';
            }
        }
		
		if(!empty($errors)){
			
			// Если есть ошибки, копируем массив $errors в $arr:
			
			$arr = $errors;
			return false;
		}
		
		// Если данные введены правильно, подчищаем данные и копируем их в $arr:
		
		foreach($data as $k=>$v){
			$arr[$k] = mysqli_real_escape_string($db, $v);
		}
		
		return true;
		
	}

	private static function validate_text($str)
	{
		/*
		/	Данный метод используется как FILTER_CALLBACK
		*/
		
		if(mb_strlen($str,'utf8')<1)
			return false;
		
		// Кодируем все специальные символы html (<, >, ", & .. etc) и преобразуем
		// символ новой строки в тег <br>:
		
		$str = nl2br(htmlspecialchars($str));
		
		// Удаляем все оставщиеся символы новой строки
		$str = str_replace(array(chr(10),chr(13)),'',$str);
		
		return $str;
	}

}

