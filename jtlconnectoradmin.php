<?php
class JTLConnectorAdmin extends oxAdminView
{ 
	protected $_sThisTemplate = "jtlconnector.tpl";

	public function render()
	{
		parent::render();

		$oxConfig = \oxRegistry::getConfig();
		include('metadata.php');

		$this->_aViewData['url'] = $oxConfig->getShopURL(null,true).'jtlconnector/';
		$this->_aViewData['pass'] = $oxConfig->getShopConfVar('password', null, 'module:jtl-connector');
		$this->_aViewData['version'] = $aModule['version'];

		return parent::render();
	}
}
