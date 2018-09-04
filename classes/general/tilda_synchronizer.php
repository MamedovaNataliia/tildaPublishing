<?php 
IncludeModuleLangFile(__FILE__);

class CTildaSynchronizer
{
		
	function setOk(){
		echo 'ok';
	}
	
	function registerTilda($arParams,$arServerParams){
		if(isset($arParams['publickey']) && $arParams['publickey'] == $arServerParams['public'] && $arParams['projectid'] == $arServerParams['site_id']){
			return true;
		}
	}
		
	function getPage($methodTitle, $isProjectIdNeeded = false, $isPageIdNeeded = false, $arParams,$arServerParams){
    	$result = file_get_contents(
    		$arServerParams['adress'].$methodTitle.'/?publickey='.$arServerParams['public'].'&secretkey='.$arServerParams['private'].($isProjectIdNeeded ? '&projectid='.$arParams['projectid'] : '').($isPageIdNeeded ? '&pageid='.$arParams['pageid'] : '')
    	);
		$data = json_decode($result, true);
		
    	return $data;
    }
}