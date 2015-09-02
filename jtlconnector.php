<?php
class JTLConnector
{ 
	public function __construct() {
		require_once __DIR__."/vendor/autoload.php";
		
		defined('CONNECTOR_DIR') || define("CONNECTOR_DIR", __DIR__);
				
		$connector = \jtl\Connector\Oxid\Oxid::getInstance();
	
		try {
		    $application = \jtl\Connector\Application\Application::getInstance();
		    $application->register($connector);
		    $application->run();
		} catch (\Exception $exc) {
		    $connector->exceptionHandler($exc);
		}				
	}

	public function onActivate()
	{
		$query = 'INSERT INTO oxseo SET 
			OXSEOURL="jtlconnector/", 
			OXSTDURL="index.php?cl=jtlconnector",
			OXOBJECTID="'.\oxUtilsObject::getInstance()->generateUID().'", 
			OXIDENT="'.md5('jtlconnector/').'", 
			OXTYPE="static",
			OXSHOPID="oxbaseshop"
		';
		
		\oxDb::getDb()->execute($query);
		
		$linkQuery = "CREATE TABLE IF NOT EXISTS jtl_connector_link (
                    endpointId char(64) NOT NULL,
                    hostId int(10) NOT NULL,
                    type int(10),
                    PRIMARY KEY (endpointId, hostId, type)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

		\oxDb::getDb()->execute($linkQuery);

		$oxConfig = \oxRegistry::getConfig();
		
		if (!$oxConfig->getShopConfVar('password', null, 'module:jtl-connector')) {
			$oxConfig->saveShopConfVar('str', 'password', substr(sha1(uniqid()), 0, 16), null, 'module:jtl-connector');
		}
	}

	public function onDeactivate()
	{
		$sQuery = 'DELETE FROM oxseo WHERE OXSEOURL="jtlconnector/"';
		\oxDb::getDb()->execute($sQuery);
	}
}
