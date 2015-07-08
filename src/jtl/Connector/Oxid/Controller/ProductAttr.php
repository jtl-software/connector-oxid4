<?php
namespace jtl\Connector\Oxid\Controller;

use \jtl\Connector\Model\ProductAttr as ProductAttrModel;
use \jtl\Connector\Oxid\Controller\ProductAttrI18n as ProductAttrI18nController;

class ProductAttr extends BaseController
{	
	public function pullData($data, $model)
	{
		$attributesResult = $this->db->getAll('
			SELECT a.*, c.* 
			FROM oxattribute a
			LEFT JOIN oxobject2attribute c ON c.OXATTRID = a.OXID
			WHERE c.OXOBJECTID = "'.$data['OXID'].'"
		');

		$attrs = array();

		$i18ns = new ProductAttrI18nController();

		foreach ($attributesResult as $attributeData) {
			$attribute = new ProductAttrModel();
			$attribute->getId()->setEndpoint($attributeData['OXID']);
			$attribute->setProductId($model->getId());
			$attribute->setIsTranslated(true);

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
			$sql->OXOBJECTID = $attr->getProductId()->getEndpoint(); 
			$sql->OXATTRID = $attr->getId()->getEndpoint();
			
			$this->db->insert($sql, 'oxobject2attribute');			
		}		
	}	
}
