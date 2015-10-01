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

        $return[0]->setIsDefault(true);
		
		return $return;
	}

	public function pushData($data)
	{
		$oxConfig = \oxRegistry::getConfig();

		$result = $oxConfig->getConfigParam('aCurrencies');

        $update = array();

		foreach ($result as $cData) {
			$cArr = explode('@ ', $cData);

			foreach ($data->getCurrencies() as $currency) {
				if ($currency->getIso() == $cArr[0]) {
					$cArr[1] = $currency->getFactor();
				}
			}

			$update[] = implode('@ ', $cArr);
		}

        $oxConfig->saveShopConfVar('arr', 'aCurrencies', $update);

		return $data;
	}
}
