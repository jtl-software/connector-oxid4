<?php
namespace jtl\Connector\Oxid\Controller;

class Category extends BaseController
{	
	private static $idCache = array();

	public function pullData($data, $model, $limit = null)
	{
		$result = $this->db->getAll('
			SELECT c.* 
			FROM oxcategories c 
			LEFT JOIN jtl_connector_link l ON c.OXID = l.endpointId AND l.type = 1
            WHERE l.hostId IS NULL 
            ORDER BY c.OXLEFT
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
		if (isset(static::$idCache[$data->getParentCategoryId()->getHost()])) {
            $data->getParentCategoryId()->setEndpoint(static::$idCache[$data->getParentCategoryId()->getHost()]);
        }

		$category = $this->mapper->toEndpoint($data);

        $category->save();

        $id = $category->save();

        \oxRegistry::get("oxSeoEncoderCategory")->markRelatedAsExpired($category);

		$data->getId()->setEndpoint($id);

		static::$idCache[$data->getId()->getHost()] = $id;

		$this->addMeta($data);

		$attrs = new CategoryAttr();
		$attrs->pushData($data);

		return $data;
	}

	public function deleteData($data)
	{
		$category = new \oxCategory();

		if (!$category->delete($data->getId()->getEndpoint())) {
			//throw new \Exception('Error deleting category with id: '.$data->getId()->getEndpoint());
		}

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
					$encoder->addSeoEntry($id, 'oxbaseshop', $langId, 'index.php?cl=alist&amp;cnid='.$id, $i18n->getUrlPath(), 'oxcategory', null, $i18n->getMetaKeywords(), $i18n->getMetaDescription());
				}
			}		
		}
	}

	public function prePush($data)
	{
		$id = $data->getId()->getEndpoint();

		if (!empty($id)) {
			$this->db->execute('DELETE FROM oxcategory2attribute where OXOBJECTID="'.$id.'"');
		}		
	}

	public function finishPush($data, $result)
	{
		$categories = new \oxCategoryList();
		$categories->updateCategoryTree();

        array_map('unlink', glob(\oxRegistry::getConfig()->getConfigParam("sCompileDir").'oxpec_oxcategory*.*'));
	}

	public function getStats()
	{
		return $this->db->GetOne('
			SELECT COUNT(*) 
			FROM oxcategories c 
			LEFT JOIN jtl_connector_link l ON c.OXID = l.endpointId AND l.type = 1
            WHERE l.hostId IS NULL
        ');
	}
}
