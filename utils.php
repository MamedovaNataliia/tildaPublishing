<?php

/**
 * Выводит на странице var_dump обрамленный в тег <pre>
 *  - если последний передаваемый параметр === true, то вызывается die;
 */
function pre_dump()
{
    $arguments	= func_get_args();
    $die		= array_pop($arguments);
    if(!is_bool($die)){
        $arguments[] = $die;
    }
    echo "<br clear='all' />";
    echo "<pre>";
    call_user_func_array('var_dump', $arguments);
    echo "</pre>";
    if($die === true){
        die;
    }
}

/**
 *  Выводит на странице var_dump обрамленный в тег <pre>, удаляет весь предшествующий вывод (для битрикса)
 *   - если последним параметром не указано false, то вызывается die;
 */

function pre_dump_clr()
{
    static $notToDiscard;
    global $APPLICATION;
    if(is_object($APPLICATION) && !$notToDiscard){
        $APPLICATION->RestartBuffer();
        $notToDiscard = true;
    }
    $arguments	= func_get_args();
    $arg_count	= count($arguments);
    if(!is_bool($arguments[$arg_count-1])){
        $arguments[] = true;
    }
    call_user_func_array('pre_dump', $arguments);
}

/**
 * Функция выводит отладочную информацию (замена pre+print_r+pre) на экран
 *
 * @param any $obj -- объект, значение которого выводят
 * @param boolean $admOnly -- функция доступна только администартору
 * @param boolean $die -- остановить выполнение скрипта
 * @return boolean
 */
function p($obj,$admOnly=true,$d=false)
{
    global $USER, $arAccessDebugFromIP;

    if(($USER->IsAdmin() || $admOnly===false) && (empty($arAccessDebugFromIP) || in_array($_SERVER["REMOTE_ADDR"], $arAccessDebugFromIP)))
    {
        echo "<pre>";
        print_r($obj);
        echo "</pre>";

        if($d===true)
            die();
    }
}

/**
 * Функция выводит отладочную информацию (замена pre+print_r+pre) в файл
 *
 * @param any $obj -- объект, значение которого выводят
 * @param boolean $admOnly -- функция доступна только администартору
 * @param boolean $die -- остановить выполнение скрипта
 * @param boolean $fileName -- файл куда будет писаться dump
 * @return boolean
 */
function p2f($obj, $admOnly=false, $die = false, $fileName = "_dump.html") {
    global $USER;
    if($admOnly===false || $USER->IsAdmin()) {
        $dump="<pre style='font-size: 11px; font-family: tahoma;'>".print_r($obj, true)."</pre>";
        $files = $_SERVER["DOCUMENT_ROOT"]."/".$fileName;
        $fp = fopen( $files, "a+" );
        fwrite( $fp, $dump);
        fclose( $fp );
        if ($die) die();
    }
}

/**
 * Функция выводит текстовую информацию в файл
 *
 * @param message  -- сообщение
 * @return boolean
 */
function l2f($message) {
    $files = $_SERVER["DOCUMENT_ROOT"]."/../log_".SITE_ID."_".date('Y.m.d').".txt";
    $fp = fopen( $files, "a+" );
    $dateTime =  date("Y.m.d H:i:s");
    fwrite( $fp, $dateTime." ".$message."\n");
    fclose( $fp );
}

/**
 * Возвращает URL текущей страницы
 */
function getRequestedUrl()
{
	$protocol = 'https';
	return $protocol.'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
}

function getAlternativeLangUrl(){
	$url = getRequestedUrl();
	$parts = explode('/', $url);
	if($parts[3] == 'ua'){
		unset($parts[3]);
		return implode('/', $parts);
	}else{
		$url = $parts[0].'/'.$parts[1].'/'.$parts[2].'/ua/';
		for($i = 0; $i <= 2; $i++){
			unset($parts[$i]);
		}
		return $url.implode('/', $parts);
	}
}
?>