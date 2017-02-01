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
			$attribute->setIsTranslated(true);

			$attribute->setI18ns($i18ns->pullData($attributeData, $attribute));

			$attrs[] = $attribute;
		}

		return $attrs;			
	}

	public function pushData($data, $model)
	{
		foreach ($data->getAttributes() as $attr) {
			$attrObj = new \stdClass();
			$valObj = new \stdClass();

			foreach ($attr->getI18ns() as $i18n) {
				$col = $this->utils->getLanguageId($i18n->getLanguageISO());
				if ($col !== false) {
					$column = $col == 0 ? '' : '_'.$col;
					$attrObj->{OXTITLE.$column} = $i18n->getName();
                }
			}

			$checkAttr = $this->db->getOne('SELECT OXID from oxattribute WHERE OXTITLE="'.$attrObj->OXTITLE.'"');

			if ($checkAttr === false) {
				$attrObj->OXID = $this->utils->oxid();
				$attrObj->OXSHOPID = 'oxbaseshop';
				$attrObj->OXDISPLAYINBASKET = 1;
				$this->db->insert($attrObj, 'oxattribute');
			} else {
				$attrObj->OXID = $checkAttr;
			}

			$valObj->OXATTRID = $attrObj->OXID;
			$valObj->OXOBJECTID = $data->getId()->getEndpoint();
			$valObj->OXID = $this->utils->oxid();

			$this->db->insert($valObj, 'oxcategory2attribute');
		}
	}
}
