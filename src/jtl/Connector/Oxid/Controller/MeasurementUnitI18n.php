<?php
namespace jtl\Connector\Oxid\Controller;

class MeasurementUnitI18n extends BaseController
{	
	public function pullData($data, $model, $limit = null)
	{
		foreach ($data['i18ns'] as $i18n) {
			$model = $this->mapper->toHost($i18n);
			
			$return[] = $model;
		}
		
		return $return;
	}
}
