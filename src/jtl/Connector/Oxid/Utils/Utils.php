<?php
namespace jtl\Connector\Oxid\Utils;

use \jtl\Connector\Session\SessionHelper;
use \jtl\Connector\Core\Utilities\Language;
use \jtl\Connector\Oxid\Utils\Db;

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

    public function getCountryIso($id)
    {
        $db = DB::getInstance();

        $country = $db->getOne('SELECT OXISOALPHA2 FROM oxcountry WHERE OXID="'.$id.'"');

        if ($country) {
            return $country;
        }
    }

    public function getState($id)
    {
        $db = DB::getInstance();

        $state = $db->getOne('SELECT OXID FROM oxstates WHERE OXTITLE="'.$id.'"');

        if ($state) {
            return $state;
        }
    }
}
