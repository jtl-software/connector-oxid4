<?php
namespace jtl\Connector\Oxid\Controller;

use \jtl\Connector\Model\ProductAttrI18n as ProductAttrI18nModel;

class ProductAttrI18n extends BaseController
{	
	public function pullData($data, $model)
	{
		$attributeI18ns = array();

		foreach ($this->utils->getLanguages() as $column => $language) {
			$column = $column == 0 ? '' : '_'.$column;

			if (!empty($data['OXTITLE'.$column]) && !empty($data['OXVALUE'.$column])) {
				$i18n = new ProductAttrI18nModel();
				$i18n->setName($data['OXTITLE'.$column]);
				$i18n->setValue($data['OXVALUE'.$column]);
				$i18n->setLanguageISO($language->iso3);
				$i18n->setProductAttrId($model->getId());

				$attributeI18ns[] = $i18n;
			}
		}

		return $attributeI18ns;			
	}
}
