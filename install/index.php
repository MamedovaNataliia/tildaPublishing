<?php

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
if (class_exists('aniart_tilda')) {
    return;
}
class aniart_tilda extends CModule
{
    public $MODULE_ID = 'aniart.tilda';
    public $MODULE_VERSION = '1.0';
    public $MODULE_VERSION_DATE = '2017-09-16';
    public $MODULE_NAME = 'Aniart - Tilda';
    public $MODULE_DESCRIPTION = 'Служит для интеграции с Tilda Publishing.';
    public $MODULE_GROUP_RIGHTS = 'N';
    public $PARTNER_NAME = "AniaArt";
    public $PARTNER_URI = "http://aniart.com.ua";


	function InstallDB()
	{
		global $DB;
		$DB->RunSQLBatch(dirname(__FILE__)."/sql/install.sql");
		return true;
	}
	function UnInstallDB()
	{
		global $DB;
		$DB->RunSQLBatch(dirname(__FILE__)."/sql/uninstall.sql");
		return true;
	}

    public function DoInstall()
    {
        global $APPLICATION;
			$this->InstallDB();
        RegisterModule($this->MODULE_ID);
    }
    public function DoUninstall()
    {
        global $APPLICATION;
			$this->UnInstallDB();
        UnRegisterModule($this->MODULE_ID);
    }
}
?>
