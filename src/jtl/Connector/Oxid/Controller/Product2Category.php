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

	public function pushData($data)
	{
		$pos = 0;

		foreach ($data->getCategories() as $category) {
			$id = $category->getCategoryId()->getEndpoint();

			if (!empty($id)) {
				$catObj = new \stdClass();
				$catObj->OXID = $this->utils->oxid();
				$catObj->OXOBJECTID = $data->getId()->getEndpoint();
				$catObj->OXCATNID = $id;
				$catObj->OXPOS = $pos;
				$catObj->OXTIME = $pos;

				$this->db->insert($catObj, 'oxobject2category');

				$pos++;
			}
		}
	}
}
