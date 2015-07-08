<?php
namespace jtl\Connector\Oxid\Controller;

class Product extends BaseController
{	
	public function pullData($data, $model, $limit = null)
	{
		$result = $this->db->getAll('
			SELECT a.*,d.*
			FROM oxarticles a 
			LEFT JOIN oxartextends d ON d.OXID = a.OXID
			LEFT JOIN jtl_connector_link l ON a.OXID = l.endpointId AND l.type = 64
            WHERE l.hostId IS NULL 
            LIMIT '.$limit
        );

		$return = array();

		foreach ($result as $data) {
			$model = $this->mapper->toHost($data);

			$return[] = $model;
		}

		return $return;
	}

	public function pushData($data)
	{
		/*
		$category = $this->mapper->toEndpoint($data);
		
		$id = $category->save();
		
		$data->getId()->setEndpoint($id);

		static::$idCache[$data->getId()->getHost()] = $id;
		*/

		return $data;
	}

	public function prePush($data)
	{
		$id = $data->getId()->getEndpoint();

		if (!empty($id)) {
			$this->db->execute('DELETE FROM oxobject2attribute where OXOBJECTID="'.$id.'"');
		}		
	}

	public function deleteData($data)
	{
		/*
		$category = new \oxCategory();

		if (!$category->delete($data->getId()->getEndpoint())) {
			throw new \Exception('Error deleting category with id: '.$data->getId()->getEndpoint());
		}
		*/
		return $data;
	}

	public function getStats()
	{
		return $this->db->GetOne('
			SELECT COUNT(*) 
			FROM oxarticles a 
			LEFT JOIN jtl_connector_link l ON a.OXID = l.endpointId AND l.type = 64
            WHERE l.hostId IS NULL
        ');
	}
}
