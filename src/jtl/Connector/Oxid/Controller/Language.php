<?php
namespace jtl\Connector\Oxid\Controller;

class Language extends BaseController
{	
	public function pullData($data, $model, $limit = null)
	{
		$return = array();

		$result = $this->utils->getLanguages();

		foreach ($result as $data) {
			$model = $this->mapper->toHost((array) $data);
			
			$return[] = $model;
		}
		
		return $return;		
	}
}
