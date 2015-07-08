<?php
namespace jtl\Connector\Oxid\Controller;

class Product2Category extends BaseController
{	
	public function pullData($data, $model, $limit = null)
	{
		$return = array();
		
		$result = $this->db->getAll('SELECT * FROM oxobject2category WHERE OXOBJECTID="'.$data['OXID'].'"');

		foreach ($result as $catData) {
			$model = $this->mapper->toHost($catData);
			
			$return[] = $model;
		}
		
		return $return;		
	}
}
