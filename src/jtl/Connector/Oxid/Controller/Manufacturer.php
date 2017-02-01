<?php
namespace jtl\Connector\Oxid\Controller;

class Manufacturer extends BaseController
{	
	public function pullData($data, $model, $limit = null)
	{
		$result = $this->db->getAll('
			SELECT m.* 
			FROM oxmanufacturers m 
			LEFT JOIN jtl_connector_link l ON m.OXID = l.endpointId AND l.type = 32
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
		$endpointModel = $this->mapper->toEndpoint($data);
		
		if (!$id = $endpointModel->save()) {
			throw new \Exception('Error saving manufacturer');
		}

		$data->getId()->setEndpoint($id);
		
		$this->addMeta($data);
		
		return $data;
	}

	private function addMeta($data)
	{
		$id = $data->getId()->getEndpoint();

		if (!empty($id)) {
			$encoder = new \oxSeoEncoder();

			foreach ($data->getI18ns() as $i18n) {
				$langId = $this->utils->getLanguageId($i18n->getLanguageISO());
				
				if ($langId !== false) {
					$encoder->addSeoEntry($id, 'oxbaseshop', $langId, null, null, null, null, $i18n->getMetaKeywords(), $i18n->getMetaDescription());
				}
			}		
		}
	}

	public function deleteData($data)
	{
		$manufacturer = new \oxManufacturer();

		if (!$manufacturer->delete($data->getId()->getEndpoint())) {
			//throw new \Exception('Error deleting manufacturer with id: '.$data->getId()->getEndpoint());
		}

		$this->db->execute('UPDATE oxarticles SET oxmanufacturerid = NULL WHERE oxmanufacturerid = "'.$data->getId()->getEndpoint().'"');

		return $data;
	}

	public function getStats()
	{
		return $this->db->GetOne('
			SELECT COUNT(*) 
			FROM oxmanufacturers m 
			LEFT JOIN jtl_connector_link l ON m.OXID = l.endpointId AND l.type = 32
            WHERE l.hostId IS NULL
        ');
	}
}
