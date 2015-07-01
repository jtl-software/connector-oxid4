<?php
namespace jtl\Connector\Oxid\Controller;

class Currency extends BaseController
{	
	public function pullData($data, $model, $limit = null)
	{
		$return = array();

		$oxConfig = \oxRegistry::getConfig();

		$result = $oxConfig->getConfigParam('aCurrencies');

		foreach ($result as $data) {
			$model = $this->mapper->toHost(explode('@ ', $data));
			
			$return[] = $model;
		}
		
		return $return;
	}
}
