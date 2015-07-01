<?php
namespace jtl\Connector\Oxid\Controller;

class Customer extends BaseController
{	
	public function pullData($data, $model, $limit = null)
	{
		$result = $this->db->getAll('
			SELECT u.*, c.OXISOALPHA3, s.OXTITLE 
			FROM oxuser u 
			LEFT JOIN oxcountry c ON c.OXID = u.OXCOUNTRYID
			LEFT JOIN oxstates s ON s.OXID = u.OXSTATEID
			LEFT JOIN jtl_connector_link l ON u.OXID = l.endpointId AND l.type = 2
            WHERE l.hostId IS NULL && u.OXID != "oxdefaultadmin"            
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
		$endpointModel = $this->mapper->toEndpoint($data);
		
		if (!$id = $endpointModel->save()) {
			throw new \Exception('Error saving customer');
		}

		$data->getId()->setEndpoint($id);
		
		return $data;
	}

	public function deleteData($data)
	{
		$user = new \oxUser();

		if (!$user->delete($data->getId()->getEndpoint())) {
			throw new \Exception('Error deleting customer with id: '.$data->getId()->getEndpoint());
		}

		return $data;
	}

	public function getStats()
	{
		return $this->db->GetOne('
			SELECT COUNT(*) 
			FROM oxuser u 
			LEFT JOIN jtl_connector_link l ON u.OXID = l.endpointId AND l.type = 2
            WHERE l.hostId IS NULL && u.OXID != "oxdefaultadmin"
        ');
	}
}
