<?php
namespace jtl\Connector\Oxid\Controller;

use \jtl\Connector\Model\CategoryAttrI18n as CategoryAttrI18nModel;

class CategoryAttrI18n extends BaseController
{	
	public function pullData($data, $model)
	{
		$attributeI18ns = array();

		foreach ($this->utils->getLanguages() as $column => $language) {
			$column = $column == 0 ? '' : '_'.$column;

			if (!empty($data['OXTITLE'.$column])) {
				$i18n = new CategoryAttrI18nModel();
				$i18n->setName($data['OXTITLE'.$column]);
				$i18n->setLanguageISO($language->iso3);
				$i18n->setCategoryAttrId($model->getId());

				$attributeI18ns[] = $i18n;
			}
		}

		return $attributeI18ns;			
	}
}
