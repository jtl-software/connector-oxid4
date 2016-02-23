<?php
class JTLConnector
{ 
	public function __construct() {
		defined('CONNECTOR_DIR') || define("CONNECTOR_DIR", __DIR__);

		$loader = require_once __DIR__."/vendor/autoload.php";
		$loader->add('', CONNECTOR_DIR . '/plugins');

		$connector = \jtl\Connector\Oxid\Oxid::getInstance();
	
		$application = \jtl\Connector\Application\Application::getInstance();
		$application->register($connector);
		$application->run();
	}

	public static function onActivate()
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
		
		$linkQuery = "
			CREATE TABLE IF NOT EXISTS jtl_connector_link (
				endpointId char(64) NOT NULL,
				hostId int(10) NOT NULL,
				type int(10)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		";

		\oxDb::getDb()->execute($linkQuery);

		if (\oxDb::getDb()->execute('SHOW INDEX FROM jtl_connector_link WHERE Key_name = "PRIMARY"')->_numOfRows > 0) {
			\oxDb::getDb()->execute('ALTER TABLE jtl_connector_link DROP PRIMARY KEY');
		}

		if (\oxDb::getDb()->execute('SHOW INDEX FROM jtl_connector_link WHERE Key_name = "endpointId"')->_numOfRows == 0) {
			\oxDb::getDb()->execute('ALTER TABLE jtl_connector_link ADD INDEX(endpointId)');
		}

		if (\oxDb::getDb()->execute('SHOW INDEX FROM jtl_connector_link WHERE Key_name = "hostId"')->_numOfRows == 0) {
			\oxDb::getDb()->execute('ALTER TABLE jtl_connector_link ADD INDEX(hostId)');
		}

		if (\oxDb::getDb()->execute('SHOW INDEX FROM jtl_connector_link WHERE Key_name = "type"')->_numOfRows == 0) {
			\oxDb::getDb()->execute('ALTER TABLE jtl_connector_link ADD INDEX(type)');
		}

		$oxConfig = \oxRegistry::getConfig();
		
		if (!$oxConfig->getShopConfVar('password', null, 'module:jtl-connector')) {
			$oxConfig->saveShopConfVar('str', 'password', substr(sha1(uniqid()), 0, 16), null, 'module:jtl-connector');
		}

        echo '<script language="JavaScript" type="text/javascript">
            url = "'.html_entity_decode($oxConfig->getActiveView()->getViewConfig()->getSelfLink()).'cl=jtlconnectoradmin";
            top.basefrm.location = url;
        </script>';
	}

	public static function onDeactivate()
	{
		$sQuery = 'DELETE FROM oxseo WHERE OXSEOURL="jtlconnector/"';
		\oxDb::getDb()->execute($sQuery);
	}
}
