<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use \Bitrix\Main\Config\Option;
use \Aniart\Main\Logger;
if($_SERVER['REQUEST_METHOD'] == 'GET'
		&& isset($_GET['publickey'])){
			$arParams = $_GET;
}
$logPath = realpath(dirname(__FILE__).'/logs/tilda.log');
$logger  = new Logger($logPath);
$logger->WriteNotice('Начало: '.date("Y.m.d H:i:s"));
if($_SERVER['REMOTE_ADDR'] !== '95.213.201.187') {
    $logger->WriteNotice('Invalid remote address ('.$_SERVER['REMOTE_ADDR'].')');
    die('0');
}

CModule::IncludeModule("aniart.tilda");
$arServerParams = array(
	"adress" => 'http://api.tildacdn.info/v1/',
	"private" => Option::get('aniart.tilda','private_key'),
	"public" => Option::get('aniart.tilda','public_key'),
	"path" => Option::get('aniart.tilda','path'),
	"site_id" => Option::get('aniart.tilda','site_id'),
	"block_id" => Option::get('aniart.tilda','aniart_tilda_iblock_id'),
	"html_destination" => Option::get('aniart.tilda', 'html_destination'),
	//"is_date_reset" => Option::get('aniart.tilda', 'is_date_reset'),
	"is_good_properties_reset" => Option::get('aniart.tilda', 'is_good_properties_reset'),
	"is_pageid_connect" => Option::get('aniart.tilda', 'is_pageid_connect')
);

//p($arServerParams);
//проверка на совпадение ID проекта
if($arParams['projectid'] != $arServerParams['site_id']){
	$logger->WriteNotice('Сайт ID не совпадает! Установлено в настройках - '.$arServerParams['site_id'].' ответ сервера - '.$arParams['projectid']);
	die();
}

if(Option::get('aniart.tilda', 'is_need_create_page') == 'Y'){
	$logger->WriteNotice('Настройки модуля: путь для записи файлов '.$arServerParams['path'].', ID инфоблока для записи '.$arServerParams['block_id']);
	if(CTildaSynchronizer::registerTilda($arParams,$arServerParams)) {
		//Информация о странице
		$fullpage = CTildaSynchronizer::getPage('getpageexport',false,true,$arParams,$arServerParams);
		$logger->WriteNotice('Получение данных: ID проекта:'.$arParams['projectid'].' ID страницы:'.$arParams['pageid'].' "'.mb_convert_encoding($fullpage['result']['title'],"Windows-1251","UTF-8")).'"';
		if (SITE_CHARSET == 'windows-1251') {
			foreach ($fullpage['result'] as $key => $item ){
				if( $key !='css' &&  $key !='js' &&  $key !='images') {
					$item = mb_convert_encoding($item,"Windows-1251","UTF-8");
					$fullpage['result'][$key]=$item;
				}
			}
		}
	
		//нформация о проекте
		$project = CTildaSynchronizer::getPage('getprojectexport', true, false,$arParams,$arServerParams);
		
		if($fullpage){
			//создаем путь для хроанения файлов
			$arParams['dir'] = $_SERVER['DOCUMENT_ROOT'].$arServerParams['path'].''.$fullpage['result']['id'].'/';
			//создаем путь для замены ссылок
			$arParams['dir_site'] = $arServerParams['path'].''.$fullpage['result']['id'].'/';
	
			//Сохраняем файлы стилей, скрипты, картинки
			CTildaTreatment::saveFile($arParams,$fullpage);
			//меняем на нормальные пути
			$fullpage = CTildaTreatment::replaceUrl($project,$fullpage,$arParams);
			//формируем отдельно скрипты, стили, мета
			$fullpage = CTildaTreatment::partitionHTML($project,$fullpage,$arParams);
	
			$el = new CIBlockElement;
	
			//Формирируем все свойства для передачи страницы
			$PROP = array(
					Option::get('aniart.tilda', 'ib_opt_js') => $fullpage['head']['js'],
					Option::get('aniart.tilda', 'ib_opt_css') => $fullpage['head']['css'],
					Option::get('aniart.tilda', 'ib_opt_body') =>$fullpage['result']['html'],
					Option::get('aniart.tilda', 'ib_opt_pageid') =>$arParams['pageid']
			);
	
			$arLoadProductArray = Array(
					"MODIFIED_BY"    => '1', // элемент изменен текущим пользователем
					"IBLOCK_ID"      => $arServerParams['block_id'],
					//"PROPERTY_VALUES"=> $PROP,
					"ACTIVE"         => "N",  // активен
					"DETAIL_TEXT_TYPE" => 'html'
					);
			if(!empty($fullpage['result']['descr']))
				$arLoadProductArray['PREVIEW_TEXT'] = $fullpage['result']['descr'];
			
			//Передавать страницу в детальный текст ?
			if($arServerParams['html_destination'] == 'Y') {
				if(Option::get('aniart.tilda', 'ib_opt_meta') == Option::get('aniart.tilda', 'ib_opt_body')){
					$logger->WriteNotice('Ошибка: заданные свойства для BODY и META статьи совпадают. Измените их.');
					$logger->WriteNotice('Окончание: '.date("Y.m.d H:i:s"));
					die;
				}
				
				if(Option::get('aniart.tilda', 'ib_opt_meta') == 'DETAIL_TEXT'){
					$arLoadProductArray['DETAIL_TEXT'] = $fullpage['head']['meta'];
					$PROP[Option::get('aniart.tilda', 'ib_opt_body')] = $fullpage['result']['html'];
				}else{
					$PROP[Option::get('aniart.tilda', 'ib_opt_meta')] = $fullpage['head']['meta'];
					
					if(Option::get('aniart.tilda', 'ib_opt_body') == 'DETAIL_TEXT'){
						$arLoadProductArray['DETAIL_TEXT'] = $fullpage['result']['html'];
					}else{
						$PROP[Option::get('aniart.tilda', 'ib_opt_body')] = $fullpage['result']['html'];
					}
				}
			} else {
				if(Option::get('aniart.tilda', 'ib_opt_meta') == 'DETAIL_TEXT'){
					$arLoadProductArray['DETAIL_TEXT'] = $fullpage['head']['meta'];
					$PROP['BODY'] = $fullpage['result']['html'];
				}else{
					$PROP[Option::get('aniart.tilda', 'ib_opt_meta')] = $fullpage['head']['meta'];
					$arLoadProductArray['DETAIL_TEXT'] = $fullpage['result']['html'];
				}
			}
	
			//Передавать символьный код,имя и изображения превью ? ?
			if($arServerParams['is_good_properties_reset'] == 'Y') {
				$params = Array(
						"max_len" => "40", // обрезает символьный код до 100 символов
						"change_case" => "L", // буквы преобразуются к нижнему регистру
						"replace_space" => "_", // меняем пробелы на нижнее подчеркивание
						"replace_other" => "_", // меняем левые символы на нижнее подчеркивание
						"delete_repeat_replace" => "true", // удаляем повторяющиеся нижние подчеркивания
						"use_google" => "false", // отключаем использование google
						);
				$arLoadProductArray['PREVIEW_PICTURE'] = CFile::MakeFileArray($fullpage['result']['img']);
				$arLoadProductArray['NAME'] = $fullpage['result']['title'];
				$arLoadProductArray['CODE'] = CUtil::translit($fullpage['result']['title'], "ru" , $params);
			}
	
			//Дата из тильды ?
			/*if($arServerParams['is_date_reset'] == 'Y') {
			$arLoadProductArray['ACTIVE_FROM'] = $DB->FormatDate($fullpage['result']['date'], "YYYY-MM-DD HH:MI:SS", FORMAT_DATETIME);
			}*/
			
			//проверяем не была ли записана уже страница
			$arPage = $el->GetList(
					Array("SORT"=>"ASC"),
					Array(
							"IBLOCK_ID" => $arServerParams['block_id'],
							"=PROPERTY_".Option::get('aniart.tilda', 'ib_opt_pageid') => $arParams['pageid']
							),
					false,
					false,
					Array()
					);
	
			$pages=array();
	
			while($page = $arPage->GetNext()) {
				$pages[]=$page;
			}
	
	
			if(count($pages) > '1') {
				//проверка на дубль страницы
				$logger->WriteNotice('Ошибка: ID страницы больше чем 1 на сайте! Должна быть уникальна');
				die();
			} elseif (count($pages) > '0') {
				//обновляемстраницу
				unset($arLoadProductArray['ACTIVE']);//уаляем признак активности, если статья отключена в админке
				$res = $el->Update($pages['0']['ID'], $arLoadProductArray);
				$res = $el->SetPropertyValuesEx($pages['0']['ID'], $arServerParams['block_id'], $PROP);
				$logger->WriteNotice('Обновлена страница ID:'.$pages['0']['ID'].' "'.$pages[0]['NAME'].'"');
			} else {
				//проверка на жесткую привязку только по ID страницы
				if($arServerParams['is_pageid_connect'] != 'Y') {
					//добавляем новую страницу
					if($PRODUCT_ID = $el->Add($arLoadProductArray)) {
						$logger->WriteNotice('Добавлена страница ID:'.$PRODUCT_ID);
					} else {
						$logger->WriteNotice('Ошибка: '.$el->LAST_ERROR);
					}
				} else {
					$logger->WriteNotice('Установлена привязка страницы по ID! '.$arParams['pageid'].' не найден в свойствах.');
				}
			}
		}
	
	}
	$logger->WriteNotice('Окончание: '.date("Y.m.d H:i:s"));
}
?>