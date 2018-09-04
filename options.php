<?
use \Bitrix\Main\Config\Option;
global $MESS;
include(GetLangFileName($GLOBALS['DOCUMENT_ROOT'].'/local/modules/aniart.tilda/lang/', '/options.php'));
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/main/options.php');

$module_id = 'aniart.tilda';
\Bitrix\Main\Loader::includeModule($module_id);

\Bitrix\Main\Loader::includeModule('iblock');
$MOD_RIGHT = $APPLICATION->GetGroupRight($module_id);
if($MOD_RIGHT>='R'):
	
	$arrTildaProjects = CTildaSynchronizer::getPage('getprojectslist', false, false, null, Array('public' => 'u2oiz8vgdwyp3tb52rmm', 'private' => 'klm1mxwkmfkgh8yhhuxu', 'adress' => 'http://api.tildacdn.info/v1/'));
	$arrTildaSiteId = Array();
	foreach ($arrTildaProjects['result'] as $project){
		$arrTildaSiteId[$project['id']] = SITE_CHARSET == 'windows-1251' ? mb_convert_encoding($project['title'],"Windows-1251","UTF-8") : $project['title'];
	}
	
	$log_data = file_get_contents($GLOBALS['DOCUMENT_ROOT'].'/local/modules/aniart.tilda/logs/tilda.log');
	
	// set up form
	$arAllOptions =	Array(
		Array('public_key', GetMessage('ANIART_TILDA_OPTIONS_PUBLIC_KEY'), 'key', Array('text')),
		Array('private_key', GetMessage('ANIART_TILDA_OPTIONS_PRIVATE_KEY'), 'key', Array('text')),
		Array('path', GetMessage('ANIART_TILDA_OPTIONS_PATH'), '/upload/tilda/', Array('text')),
		Array('ib_opt_css', GetMessage('ANIART_TILDA_OPTIONS_IBLOCK_OPT_CSS'), 'CSS', Array('text')),
		Array('ib_opt_js', GetMessage('ANIART_TILDA_OPTIONS_IBLOCK_OPT_JS'), 'JS', Array('text')),
		Array('ib_opt_body', GetMessage('ANIART_TILDA_OPTIONS_IBLOCK_OPT_BODY'), 'BODY', Array('text')),
		Array('ib_opt_pageid', GetMessage('ANIART_TILDA_OPTIONS_IBLOCK_OPT_PAGEID'), 'PAGE_ID', Array('text')),
		Array('ib_opt_meta', GetMessage('ANIART_TILDA_OPTIONS_IBLOCK_OPT_META'), 'DETAIL_TEXT', Array('text')),
		Array('site_id', GetMessage('ANIART_TILDA_OPTIONS_TILDA_SITE'), null, Array('selectbox', $arrTildaSiteId)),
		Array("html_destination", GetMessage("ANIART_TILDA_OPTIONS_HTML_DESTINATION"), 'default', Array("checkbox", "Y")),
		Array("is_need_create_page", GetMessage("ANIART_TILDA_OPTIONS_IS_NEED_CREATE_PAGE"), 'default', Array("checkbox", "Y")),
		//Array("is_date_reset", GetMessage("ANIART_TILDA_OPTIONS_IS_DATE_RESET"), 'default', Array("checkbox", "Y")),
		Array("is_good_properties_reset", GetMessage("ANIART_TILDA_OPTIONS_IS_GOOD_PROP_RESET"), 'default', Array("checkbox", "Y")),
		Array("is_pageid_connect", GetMessage("ANIART_TILDA_OPTIONS_IS_PAGEID_CONNECT"), 'default', Array("checkbox", "Y")),
		Array("tilda_log", GetMessage("ANIART_TILDA_OPTIONS_LOG"), '', Array("textarea"))
	);

if($MOD_RIGHT>='Y' || $USER->IsAdmin()):

	if ($REQUEST_METHOD=='GET' && strlen($RestoreDefaults)>0 && check_bitrix_sessid())
	{
		Option::delete($module_id);
		$z = CGroup::GetList($v1='id',$v2='asc', array('ACTIVE' => 'Y', 'ADMIN' => 'N'));
		while($zr = $z->Fetch())
			$APPLICATION->DelGroupRight($module_id, array($zr['ID']));
	}

	if($REQUEST_METHOD=='POST' && strlen($Update)>0 && check_bitrix_sessid())
	{
		$arOptions = $arAllOptions;
		foreach($arOptions as $option)
		{
			if(!is_array($option) || isset($option['note']))
				continue;
			$name = $option[0];
			$val = ${$name};
			Option::set($module_id, $name, $val);
		}
		Option::set($module_id, 'aniart_tilda_iblock_id', $_POST['aniart_tilda_iblock_id']);
		Option::set($module_id, 'aniart_tilda_iblock_type_id', $_POST['aniart_tilda_iblock_type_id']);
	}

	Option::set($module_id, 'tilda_log', $log_data);

	
endif; //if($MOD_RIGHT>="W"):

$aTabs = array();
$aTabs[] = array('DIV' => 'set', 'TAB' => GetMessage('MAIN_TAB_SET'), 'ICON' => 'aniart_tilda_settings', 'TITLE' => GetMessage('MAIN_TAB_TITLE_SET'));
$aTabs[] = array('DIV' => 'rights', 'TAB' => GetMessage('MAIN_TAB_RIGHTS'), 'ICON' => 'aniart_tilda_settings', 'TITLE' => GetMessage('MAIN_TAB_TITLE_RIGHTS'));

$tabControl = new CAdminTabControl('tabControl', $aTabs);
?>
<?
$tabControl->Begin();
?>
<style>
table.edit-table td.field-name  {
	width: 40% !important;
}
textarea{
	width:100%;
	height:200px;
}
</style>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($mid)?>&lang=<?=LANGUAGE_ID?>" name="aniart_tilda_settings">
<?$tabControl->BeginNextTab();?>
<?__AdmSettingsDrawList('aniart.tilda', $arAllOptions);?>
<?
$aniart_tilda_iblock_id = Option::get($module_id, 'aniart_tilda_iblock_id');
?>
	<tr>
		<td></td>
		<td> <input type="button" onclick="clearLog()" class='adm-workarea adm-input' value='<?=GetMessage('ANIART_TILDA_PANELS_CLEAR_LOG')?>'></input> </td>
	</tr>
	<tr>
		<td><?echo GetMessage('ANIART_TILDA_OPTIONS_IBLOCK_ID')?></td>
		<td><?echo GetIBlockDropDownList($aniart_tilda_iblock_id, 'aniart_tilda_iblock_type_id', 'aniart_tilda_iblock_id', false, 'class="adm-detail-iblock-types"', 'class="adm-detail-iblock-list"');?></td>
	</tr>
	<?/*<tr>
		<td></td>
		<td> <input type="button" onclick="ibProcessing()" class='adm-workarea adm-input' value='<?=GetMessage('ANIART_TILDA_PANELS_ADD_IBLOCK_PROPERTIES')?>'></input> </td>
	</tr>*/?>
	<tr>
		<td></td>
		<td>
			<span id='iblock_processing_status'></span>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?=BeginNote();?>
			<p>
				<span class="required"><sup>1</sup></span>
				<?=GetMessage("ANIART_EVENT_MESSAGE_HEADR_1")?>
				<div style="margin-left: 20px;"><?echo GetMessage('ANIART_EVENT_MESSAGE_BODY_1')?></div>
			</p>
			<p>
				<span class="required"><sup>2</sup></span>
				<?=GetMessage("ANIART_EVENT_MESSAGE_HEADR_2")?>
				<div style="margin-left: 20px;"><?=GetMessage("ANIART_EVENT_MESSAGE_BODY_2")?><?=': '.$_SERVER['HTTP_HOST'].'/local/modules/aniart.tilda/get.php';?></div>
			</p>
			<?=EndNote(); ?>
		</td>
	</tr>
	
	
	
	
<?$tabControl->BeginNextTab();?>
<?require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/admin/group_rights.php');?>
<?$tabControl->Buttons();?>
<script language="JavaScript">
function RestoreDefaults()
{
	if(confirm('<?echo AddSlashes(GetMessage('MAIN_HINT_RESTORE_DEFAULTS_WARNING'))?>'))
		window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)."&".bitrix_sessid_get();?>";
}
function ibProcessing()
{
	var xhr = new XMLHttpRequest();
	var iblockid = document.getElementById('aniart_tilda_iblock_id');
	
	var params = 'iblockid=' + iblockid.value;
	xhr.open("GET", '/local/modules/aniart.tilda/iblock.php?' + params, true);
	xhr.onreadystatechange = function(){
		document.getElementById('iblock_processing_status').innerHTML = xhr.response;
	}

	xhr.send();
}
function clearLog(){
	var xhr = new XMLHttpRequest();
	
	var params = 'clearlog=Y';
	xhr.open("GET", '/local/modules/aniart.tilda/ajax.php?' + params, true);
	xhr.onreadystatechange = function(){
		location.reload();
		//document.getElementsByName('tilda_log').value = '';
	}

	xhr.send();	
}
</script>
<input type="submit" name="Update" <?if ($MOD_RIGHT<'W') echo "disabled" ?> value="<?echo GetMessage('MAIN_SAVE')?>">
<input type="reset" name="reset" value="<?echo GetMessage('MAIN_RESET')?>">
<input type="hidden" name="Update" value="Y">
<?=bitrix_sessid_post();?>
<input type="button" <?if ($MOD_RIGHT<'W') echo "disabled" ?> title="<?echo GetMessage('MAIN_HINT_RESTORE_DEFAULTS')?>" OnClick="RestoreDefaults();" value="<?echo GetMessage('MAIN_RESTORE_DEFAULTS')?>">
<?$tabControl->End();?>
</form>
<?endif;
?>