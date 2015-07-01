<?php
namespace jtl\Connector\Oxid\Controller;

class MeasurementUnit extends BaseController
{	
	public function pullData($data, $model, $limit = null)
	{
		$return = array();
		$units = array();

		foreach ($this->utils->getLanguages() as $key => $language) {
			$langUnits = \oxRegistry::getLang()->getSimilarByKey("_UNIT_", $key, false);

			foreach ($langUnits as $id => $name) {
				$units[$id]['id'] = $id;
				$units[$id]['i18ns'][$language->iso3] = array(
					'name' => $name,
					'iso' => $language->iso3,
					'unitId' => $id
				);
			}
		}
	
		foreach ($units as $data) {
			$model = $this->mapper->toHost($data);
			
			$return[] = $model;
		}
		
		return $return;
	}
}
