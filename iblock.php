<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');?>
<?
	use \Bitrix\Main\Config\Option;
	CModule::IncludeModule("aniart.tilda");
?>
<?
	if($_SERVER['REQUEST_METHOD'] == 'GET'){
		$res = '';
		$ibp = new CIBlockProperty;
		
		$ib_prop_css = Option::get('aniart.tilda', 'ib_opt_css');
		$ib_prop_js = Option::get('aniart.tilda', 'ib_opt_js');
		$ib_prop_body = Option::get('aniart.tilda', 'ib_opt_body');
		$ib_prop_pageid = Option::get('aniart.tilda', 'ib_opt_pageid');
		
		// CSS Iblock property checking
		$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$_GET['iblockid'], "CODE"=>$ib_prop_css));
		if ($prop_fields = $properties->GetNext())
		{
		  $res .= ' "'.$ib_prop_css.'"';
		}
		else
		{
			//добавление свойства CSS
			$arFields = Array(
					"NAME" => "Стили",
					"ACTIVE" => "Y",
					"SORT" => "600",
					"CODE" => "CSS",
					"PROPERTY_TYPE" => "S",
					"USER_TYPE" => "HTML",
					"IBLOCK_ID" => $_GET['iblockid'],
			);
			$PropID = $ibp->Add($arFields);
		}
		
		// JS Iblock property checking
		$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$_GET['iblockid'], "CODE"=>$ib_prop_js));
		if ($prop_fields = $properties->GetNext())
		{
			$res .= ' "'.$ib_prop_js.'"';
		}
		else
		{
			//добавление свойства JS
			$arFields = Array(
					"NAME" => "Скрипты",
					"ACTIVE" => "Y",
					"CODE" => "JS",
					"PROPERTY_TYPE" => "S",
					"USER_TYPE" => "HTML",
					"IBLOCK_ID" => $_GET['iblockid'],
					);
			$PropID = $ibp->Add($arFields);
		}
		
		if(Bitrix\Main\Config\Option::get('aniart.tilda', 'html_destination') == 'Y'){
			// BODY Iblock property checking
			$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$_GET['iblockid'], "CODE"=>$ib_prop_body));
			if ($prop_fields = $properties->GetNext())
			{
				$res .= ' "'.$ib_prop_body.'"';
			}
			else
			{
				//добавление свойства BODY
				$arFields = Array(
						"NAME" => "Body",
						"ACTIVE" => "Y",
						"CODE" => "BODY",
						"PROPERTY_TYPE" => "S",
						"USER_TYPE" => "HTML",
						"IBLOCK_ID" => $_GET['iblockid'],
						);
				$PropID = $ibp->Add($arFields);
			}	
		}
		
		// PAGE_ID Iblock property checking
		$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$_GET['iblockid'], "CODE"=>$ib_prop_pageid));
		if ($prop_fields = $properties->GetNext())
		{
			$res .= ' "'.$ib_prop_pageid.'"';
		}
		else
		{
			//добавление свойства PAGEID
			$arFields = Array(
					"NAME" => "Идентификатор статьи",
					"ACTIVE" => "Y",
					"CODE" => "PAGE_ID",
					"PROPERTY_TYPE" => "S",
					"USER_TYPE" => "HTML",
					"IBLOCK_ID" => $_GET['iblockid'],
					);
			$PropID = $ibp->Add($arFields);
		}
		
		echo !empty($res) ? "Свойство ".$res." в выбраном инфоблоке уже существует" : 'Свойства успешно созданы!';
		
	}
?>
