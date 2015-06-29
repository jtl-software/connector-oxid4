<?php
namespace jtl\Connector\Oxid\Controller;

use \jtl\Connector\Model\CategoryAttr as CategoryAttrModel;
use \jtl\Connector\Oxid\Controller\CategoryAttrI18n as CategoryAttrI18nController;

class CategoryAttr extends BaseController
{	
	public function pullData($data, $model)
	{
		$attributesResult = $this->db->getAll('
			SELECT a.* 
			FROM oxattribute a
			LEFT JOIN oxcategory2attribute c ON c.OXATTRID = a.OXID
			WHERE c.OXOBJECTID = "'.$data['OXID'].'"
		');

		$attrs = array();

		$i18ns = new CategoryAttrI18nController();

		foreach ($attributesResult as $attributeData) {
			$attribute = new CategoryAttrModel();
			$attribute->getId()->setEndpoint($attributeData['OXID']);
			$attribute->setCategoryId($model->getId());

			$attribute->setI18ns($i18ns->pullData($attributeData, $attribute));

			$attrs[] = $attribute;
		}

		return $attrs;			
	}

	public function pushData($data, $model)
	{
		foreach ($data->getAttributes() as $attr) {
			$sql = new \stdClass;
			$sql->OXID = $this->utils->oxid();
			$sql->OXOBJECTID = $attr->getCategoryId()->getEndpoint(); 
			$sql->OXATTRID = $attr->getId()->getEndpoint();
			
			$this->db->insert($sql, 'oxcategory2attribute');			
		}		
	}
}
