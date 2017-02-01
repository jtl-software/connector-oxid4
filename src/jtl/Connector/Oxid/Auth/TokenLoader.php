<?php
namespace jtl\Connector\Oxid\Auth;

use \jtl\Connector\Authentication\ITokenLoader;

class TokenLoader implements ITokenLoader
{
    public function load()
    {
        $oxConfig = \oxRegistry::getConfig();
		return $oxConfig->getShopConfVar('password', null, 'module:jtl-connector');
    }
}
