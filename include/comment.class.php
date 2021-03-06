<?php

/**
 * Class: Comment
 *
 */
class Comment
{
	private $data = array();
	private $uuid = array();
	
    /**
     * rdate
     *
     * @param mixed $format
     * @param mixed $timestamp
     * @param int $case
     */
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

	/**
	 * __construct
	 *
	 * @param mixed $row
	 * @param mixed $uuid
	 */
	public function __construct($row = null, $uuid = null )
	{
		$this->data = $row;
		$this->uuid = $uuid;
	}
	
	/**
	 * markup
	 *
	 * @param mixed $db
	 */
	public function markup(&$db)
	{
		/*
		/	Данный метод выводит разметку XHTML для комментария
		*/
		
		// Устанавливаем псевдоним, чтобы не писать каждый раз $this->data:
		$d = &$this->data;
        $uuid = &$this->uuid;
		
		$link_open = '';
		$link_close = '';
		
		// Преобразуем время в формат UNIX:
		$d['date'] = strtotime($d['date']);
		
		// Нужно для установки изображения по умолчанию:
		$url = 'http://'. $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR . '/img/default_avatar.gif';
		
		$result = $db->query("	SELECT COUNT(*)
								FROM `". DB_TABLE ."`
								WHERE `id`='". $d['id'] ."'
									AND `uuid`=UNHEX('". $d['uuid'] ."')
									AND NOT `public`
								LIMIT 1;");
        $result = $result->fetch_array();

        $controll_button = false;
        $controll_button_start = '  <div class="controll-button">';

        $ip='';

        if(Comment::is_admin_uuid($uuid, $db)) {

            if($d['public']) {
                $controll_button_admin ='<button class="unpublic" onclick="unpublic_comment('. $d['id'] .')" >
                                            <img src="http://' . $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR . '/img/unpublic.png" />
                                            <span>Скрыть</span>
                                        </button>';
            } else {
                $controll_button_admin ='<button class="public" onclick="public_comment('. $d['id'] .')" >
                                            <img src="http://' . $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR . '/img/public.png" />
                                            <span>Публиковать</span>
                                        </button>';
            }
            $controll_button = true;
            $result[0] = 1;
            $ip = '<div id="ip">IP: ' . $d['ip'] . '</div>';
        }

        if ($result[0] == 1) {
            $controll_button_user  = '  <button class="edit" onclick="edit_comment('. $d['id'] .')" >
                                            <img src="http://' . $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR . '/img/edit.png" />
                                            <span>Редактировать</span>
                                        </button>
                                        <button class="delete" onclick="delete_comment('. $d['id'] .')" >
                                            <img src="http://' . $_SERVER['SERVER_NAME'] . '/' . ROOT_DIR . '/img/delete.png" />
                                            <span>Удалить</span>
                                        </button>';
            $controll_button = true;
        }

        $controll_button_end = '</div>';

        $controll_button = $controll_button ? $controll_button_start . $controll_button_admin . $controll_button_user . $controll_button_end : '';

		return '

            <div class="comment" id="comment-'. $d['id'] .'">
				<div class="avatar">
					'.$link_open.'
					<img src="http://www.gravatar.com/avatar/'.md5($d['email']).'?size=50&amp;default='.urlencode($url).'" />
					'.$link_close.'
				</div>
				
                <div class="name">'.$link_open.$d['name'].$link_close.'</div>'
                . $ip .
				'<div class="date" title="Добавлен '. $this->rdate('d F Y \в H:i', $d['date'], 1) .'">'. $this->rdate('d F Y', $d['date'], 1) .'</div>
                <div class="content">'.htmlspecialchars_decode($d['body']).'</div>'.
                $controll_button .
			'</div>
		';
	}
	
	/**
	 * validate
	 *
	 * @param mixed $arr
	 * @param mixed $db
	 */
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
		
		if(!($data['body'] = filter_input(INPUT_POST,'body',FILTER_CALLBACK,array('options'=>'Comment::validate_body'))))
		{
			$errors['body'] = 'Пожалуйста, введите текст комментария.';
		}
		

		if(!($data['name'] = filter_input(INPUT_POST,'name',FILTER_CALLBACK,array('options'=>'Comment::validate_text'))))
		{
			$errors['name'] = 'Пожалуйста, введите имя.';
        } elseif (mb_strlen($data['name'], 'UTF-8') < 2) {
			$errors['name'] = 'Пожалуйста, введите имя, не меньше 2 символов.';
        } elseif (!Comment::is_admin_uuid($data['uuid'], $db)) {
            Comment::validate_name($data, $errors, $db); 
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

	/**
	 * validateEdit
	 *
	 * @param mixed $arr
	 * @param mixed $db
	 */
	public static function validateEdit(&$arr, &$db)
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
        Comment::validateCommentID($data, $errors, $db);

		// Используем функцию filter_input, введенную в PHP 5.2.0

        $data['ip'] = $_SERVER['REMOTE_ADDR'];

		if(!($data['body'] = filter_input(INPUT_POST,'body',FILTER_CALLBACK,array('options'=>'Comment::validate_body'))))
		{
			$errors['body'] = 'Пожалуйста, введите текст комментария.';
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
		
		return true;
		
	}

	/**
	 * validateMore
	 *
	 * @param mixed $arr
	 * @param mixed $db
	 */
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

	/**
	 * validateAction
	 *
	 * @param mixed $arr
	 * @param mixed $db
	 */
	public static function validateAction(&$arr, &$db)
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
        Comment::validateCommentID($data, $errors, $db);
		
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

	/**
	 * validate_name
	 *
	 * @param mixed $data
	 * @param mixed $errors
	 * @param mixed $db
	 */
	private static function validate_name(&$data, &$errors, &$db)
	{
        $result = $db->query("SELECT `name` FROM `". DB_TABLE_STOP_WORDS ."`;");

        while($row = $result->fetch_row()) {
            if (mb_stripos($data['name'], $row[0], 0, 'UTF-8') !== false) {
                $errors['name'] = 'Вы не можете использовать "'. $row[0] .'" в своем имени.';
            }
        }

        $result->free();
		
	}

	/**
	 * validate_body
	 *
	 * @param mixed $str
	 */
	private static function validate_body($str)
	{
        $tags = '<a><br><em><p><strong><span>';
        $str = Comment::validate_text($str, $tags);
		
		return $str;
	}

	/**
	 * validate_text
	 *
	 * @param mixed $str
	 * @param string $tags
	 */
	private static function validate_text($str, $tags = '')
	{
		/*
		/	Данный метод используется как FILTER_CALLBACK
		*/
		
		if(mb_strlen($str,'utf8')<1)
			return false;
		
        // Оставляем только разрешонные теги
        $str = strip_tags($str, $tags);
		// Кодируем все специальные символы html (<, >, ", & .. etc) и преобразуем
		// символ новой строки в тег <br>:
		
		$str = nl2br(htmlspecialchars($str));
		
		// Удаляем все оставщиеся символы новой строки
		$str = str_replace(array(chr(10),chr(13)),'',$str);
		
		return $str;
	}

    /**
     * is_admin
     *
     */
    public static function is_admin()
    {
        $adminkey = $_GET['adminkey'];

        if(md5($adminkey) === ADMIN_KEY_MD5)
            return true;
        return false;
    }

    /**
     * is_admin_uuid
     *
     * @param mixed $uuid
     * @param mixed $db
     */
    public static function is_admin_uuid($uuid, &$db)
    {
        if($uuid === Comment::getOption('admin', $db))
            return true;
        return false;
    }

    /**
     * validateUuid
     *
     * @param mixed $data
     * @param mixed $errors
     */
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

    /**
     * validateUrl
     *
     * @param mixed $data
     * @param mixed $errors
     */
    private static function validateUrl(&$data, &$errors)
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

    /**
     * validateCommentID
     *
     * @param mixed $data
     * @param mixed $errors
     * @param mixed $db
     */
    private static function validateCommentID(&$data, &$errors, &$db)
    {
        if(!($data['commentID'] = filter_input(INPUT_POST,'commentID',FILTER_VALIDATE_INT))) {
		    $errors['commentID'] = 'Пожалуйста, введите правильный commentID.';
        } elseif(!Comment::is_admin_uuid($data['uuid'], $db)) {
            $result = $db->query("SELECT COUNT(*) FROM `". DB_TABLE ."` WHERE `id`='". $data['commentID'] ."' AND `uuid`=UNHEX('". $data['uuid'] ."');");
            $result = $result->fetch_array();
            if($result[0] != 1) {
                $errors['commentID'] = 'Вам нельзя редактировать и удалять этот комментарий';
            }
        }
    }

    /**
     * setOption
     *
     * @param mixed $option_name
     * @param mixed $option_value
     * @param mixed $db
     */
    public static function setOption($option_name, $option_value, &$db)
    {
        $option_value = mysqli_escape_string($db, $option_value);

        $db->query("UPDATE `". DB_TABLE_OPTIONS ."`
                    SET `value`='". $option_value ."'
                    WHERE `name`='". $option_name ."'
                    LIMIT 1;
        ");
    }
		
    /**
     * getOption
     *
     * @param mixed $option_name
     * @param mixed $db
     */
    public static function getOption($option_name, &$db)
    {
        $result = $db->query("  SELECT `value`
                                FROM `". DB_TABLE_OPTIONS ."`
                                WHERE `name`='". $option_name ."'
                                LIMIT 1;
        ");
        $row = $result->fetch_row();

        return $row[0];
    }
}

