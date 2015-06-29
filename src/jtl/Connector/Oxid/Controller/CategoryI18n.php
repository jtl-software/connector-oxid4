<?php
namespace jtl\Connector\Oxid\Controller;

use \jtl\Connector\Model\CategoryI18n as CategoryI18nModel;

class CategoryI18n extends BaseController
{	
	public function pullData($data, $model)
	{
		$i18ns = array();

		foreach ($this->utils->getLanguages() as $column => $language) {
			$column = $column == 0 ? '' : '_'.$column;

			if (!empty($data['OXTITLE'.$column])) {
				$i18n = new CategoryI18nModel();
				$i18n->setName($data['OXTITLE'.$column]);
				$i18n->setDescription($data['OXLONGDESC'].$colum);
				$i18n->setLanguageISO($language->iso3);
				$i18n->setCategoryId($model->getId());

				$i18ns[] = $i18n;
			}
		}

		return $i18ns;			
	}

	public function pushData($data, $model)
	{
		foreach ($data->getI18ns() as $i18n) {
			$id = $this->utils->getLanguageId($i18n->getLanguageISO());
			
			if ($id !== false) {
				$column = $id == 0 ? '' : '_'.$id;

				$model->addFieldName('oxtitle'.$column);
				$model->addFieldName('oxlongdesc'.$column);

				$model->assign(array(
					'oxtitle'.$column => $i18n->getName(),
					'oxlongdesc'.$column => $i18n->getDescription()					
				));
			}
		}
	}
}
