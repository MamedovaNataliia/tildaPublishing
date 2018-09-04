<?php 
IncludeModuleLangFile(__FILE__);

class CTildaTreatment
{

	function saveFile($arParams,$fullpage){
		
		// css
		$dir = $arParams['dir'].'css/';
		if(!is_dir($dir)) mkdir($dir, 0755, true) ;
		foreach ($fullpage['result']['css'] as $item){
			$fileRead = file_get_contents($item['from']);
 			$fileSave = fopen($dir.$item['to'], "w");
			fwrite($fileSave, $fileRead);	 
			fclose($fileSave);
		}
	
		// js
		$dir = $arParams['dir'].'js/';
		if(!is_dir($dir)) mkdir($dir, 0755, true) ;
		foreach ($fullpage['result']['js'] as $item){
			$fileRead = file_get_contents($item['from']);
 			$fileSave = fopen($dir.$item['to'], "w");
			fwrite($fileSave, $fileRead);	 
			fclose($fileSave);
		}
	
		// images
		$dir = $arParams['dir'].'img/';
		if(!is_dir($dir)) mkdir($dir, 0755, true) ;
		foreach ($fullpage['result']['images'] as $item){
			$fileRead = file_get_contents($item['from']);
 			$fileSave = fopen($dir.$item['to'], "w");
			fwrite($fileSave, $fileRead);	 
			fclose($fileSave);
		}
		$fileRead = file_get_contents($fullpage['result']['img']);
		$fileSave = fopen($dir.basename($fullpage['result']['img']), "w");
		fwrite($fileSave, $fileRead);
		fclose($fileSave);
		
	}
	
	function replaceUrl($project,$fullpage,$arParams){
		//
		$search = $project['result']['export_imgpath'];
		$replace = $arParams['dir_site'].'img';
		$fullpage['result']['html'] = str_replace($search, $replace, $fullpage['result']['html']);
		
		$search = $project['result']['export_csspath'];
		$replace = $arParams['dir_site'].'css';
		$fullpage['result']['html'] = str_replace($search, $replace, $fullpage['result']['html']);
		
		$search = $project['result']['export_jspath'];
		$replace = $arParams['dir_site'].'js';
		$fullpage['result']['html'] = str_replace($search, $replace, $fullpage['result']['html']);
		
		return $fullpage;
	}
	
	function partitionHTML($project,$fullpage,$arParams){
// 		preg_match('|<body [^>]*>(.*?)</body>|Uis', $fullpage['result']['html'], $fullpage['result']['body']);
// 		preg_match('~<head>(.*?)</head>~is', $fullpage['result']['html'], $fullpage['result']['head']);
		
		foreach ($fullpage['result']['css'] as $item){
			$fullpage['head']['css'][] = $arParams['dir_site'].'css/'.$item['to'];
		}
		
		foreach ($fullpage['result']['js'] as $item){
			$fullpage['head']['js'][] = $arParams['dir_site'].'js/'.$item['to'];
		}
		
		$img_title = !empty($fullpage['result']['img']) ? basename($fullpage['result']['img']) : basename($fullpage['result']['fb_img']);
		$site= 'http://'.$_SERVER['HTTP_HOST'];
		$meta = [
		    '<meta property="og:url" content="'.$site.'/'.$fullpage['result']['alias'].'" />',
            '<meta property="og:title" content="'.$fullpage['result']['title'].'" />',
            '<meta property="og:description" content="'.$fullpage['result']['descr'].'" />',
            '<meta property="og:type" content="website" />',
            '<meta property="og:image" content="'.$site.$arParams['dir_site'].'img/'.$img_title.'" />',
            '<meta name="twitter:card" content="summary"/>',
            '<meta name="twitter:site" content="@"/>',
            '<meta name="twitter:title" content="'.$fullpage['result']['title'].'" />',
            '<meta name="twitter:description" content="'.$fullpage['result']['descr'].'" />',
            '<meta name="twitter:image" content="'.$site.$arParams['dir_site'].'img/'.$img_title.'" />',
            ''
        ];
		$fullpage['head']['meta'] = implode("\n", $meta);

		return $fullpage;
	}
}