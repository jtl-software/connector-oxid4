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
	
	public function pushData($data)
	{
		foreach ($data->getAttributes() as $attr) {
			$attrObj = new \stdClass();
			$valObj = new \stdClass();

            foreach ($attr->getI18ns() as $i18n) {
                $col = $this->utils->getLanguageId($i18n->getLanguageISO());
                if ($col !== false) {
                    $column = $col == 0 ? '' : '_'.$col;
                    $attrObj->{OXTITLE.$column} = $i18n->getName();
                    $valObj->{OXVALUE.$column} = $i18n->getValue();
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

            $this->db->insert($valObj, 'oxobject2attribute');
		}

		// also turn variation values into attributes on push?
	}	
}
