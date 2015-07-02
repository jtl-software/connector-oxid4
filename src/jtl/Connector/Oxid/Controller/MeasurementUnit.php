<?php
namespace jtl\Connector\Oxid\Controller;

class MeasurementUnit extends BaseController
{	
	public function pullData($data, $model, $limit = null)
	{
		$return = array();
		$units = array();

        $additionalUnits = $this->db->getAll('SELECT OXUNITNAME FROM oxarticles WHERE OXUNITNAME IS NOT NULL GROUP BY OXUNITNAME');

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

            foreach ($additionalUnits as $aUnit) {
                $units[$aUnit['OXUNITNAME']]['id'] = $aUnit['OXUNITNAME'];
                $units[$aUnit['OXUNITNAME']]['i18ns'][$language->iso3] = array(
                    'name' => $aUnit['OXUNITNAME'],
                    'iso' => $language->iso3,
                    'unitId' => $aUnit['OXUNITNAME']
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
