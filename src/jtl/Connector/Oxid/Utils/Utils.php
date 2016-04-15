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
			$oxLang = new \oxLang();

			foreach($oxLang->getLanguageArray() as $language) {
				$obj = new \stdClass;
				$obj->iso2 = $language->oxid;
				$obj->iso3 = Language::convert($language->oxid);
				$obj->name = $language->name;
				$obj->column = $language->id;
				$obj->default = $oxConfig->getConfigParam('sDefaultLang') == $language->id ? true : false;

				$languages[$language->id] = $obj;
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
        $db = Db::getInstance();

        $country = $db->getOne('SELECT OXISOALPHA2 FROM oxcountry WHERE OXID="'.$id.'"');

        if ($country) {
            return $country;
        }
    }

    public function getState($id)
    {
        $db = Db::getInstance();

        $state = $db->getOne('SELECT OXID FROM oxstates WHERE OXTITLE="'.$id.'"');

        if ($state) {
            return $state;
        }
    }

	public function decode($field)
	{
		$oxConfig = \oxRegistry::getConfig();
		return $oxConfig->getDecodeValueQuery($field);
	}
}
