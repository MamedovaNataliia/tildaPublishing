<?

if(!CModule::IncludeModule('iblock'))
	return false;

IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	'aniart.tilda',
	array(
		'CTilda' => 'classes/general/tilda.php',
		'CTildaSynchronizer' => 'classes/general/tilda_synchronizer.php',
		'CTildaTreatment' => 'classes/general/tilda_treatment.php',
	)
);

//Подключаем рабочие файлы
$modulePath = dirname(__FILE__);
//include $modulePath.'/utils.php';
include $modulePath.'/events.php';
?>