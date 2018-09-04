<?require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');?>
<?
$module_id = 'aniart.tilda';

	if($_SERVER['REQUEST_METHOD'] == 'GET'){
		if(isset($_GET['clearlog']) && $_GET['clearlog'] == 'Y'){
			file_put_contents($GLOBALS['DOCUMENT_ROOT'].'/local/modules/aniart.tilda/logs/tilda.log', '');
			Option::set($module_id, 'tilda_log', '');
		}
	}
?>