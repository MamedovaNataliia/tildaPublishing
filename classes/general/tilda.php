<?php

use \Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Localization\Loc;

IncludeModuleLangFile(__FILE__);

class CTilda
{
	/**
	 *
	 *
	 * @var CIBlockElement
	 */
	var $cIB_E = null;

	/** @var ErrorCollection */
	protected $errorCollection;

	function __construct()
	{
		$this->cIB_E = new CIBlockElement();
		$this->errorCollection = new ErrorCollection();
	}

	function Add()
	{

	}

	function Update()
	{

	}

	function Delete()
	{

	}

	public function getErrors()
	{
		return $this->errorCollection;
	}

}

?>