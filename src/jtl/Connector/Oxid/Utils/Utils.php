<?php
namespace jtl\Connector\Oxid\Utils;

use \jtl\Connector\Session\SessionHelper;
use \jtl\Connector\Core\Utilities\Language;

class Utils {
	private static $instance;
	private $session = null;
	
	public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct()
    {
    	$this->session = new SessionHelper("oxidConnector");
    }

	public static function oxid()
	{
		return \oxUtilsObject::getInstance()->generateUID();
	}

	public function getLanguages()
	{
		if (is_null($this->session->languages)) {
			$languages = array();
			$oxConfig = \oxRegistry::getConfig();
			$column = 0;

			foreach ($oxConfig->getShopConfVar('aLanguages') as $iso2 => $name) {
				$obj = new \stdClass;
				$obj->iso2 = $iso2;
				$obj->iso3 = Language::convert($iso2);
				$obj->name = $name;
				$obj->column = $column;
				
				$languages[$column] = $obj;

				$column++;
			}

			$this->session->languages = $languages;		
		}		

		return $this->session->languages;
	}

	public function getLanguageId($iso)
	{
		foreach ($this->getLanguages() as $key => $language) {
			if ($language->iso3 === $iso) {
				return $key;
			}
		}

		return false;
	}
}
