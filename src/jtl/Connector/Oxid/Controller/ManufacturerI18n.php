<?php
namespace jtl\Connector\Oxid\Controller;

use \jtl\Connector\Model\ManufacturerI18n as ManufacturerI18nModel;

class ManufacturerI18n extends BaseController
{	
	public function pullData($data, $model)
	{
		$i18ns = array();

		$seoEncoder = new \oxSeoEncoder();

		foreach ($this->utils->getLanguages() as $key => $language) {
			$column = $key === 0 ? '' : '_'.$key;

			$keywords = $seoEncoder->getMetaData($data['OXID'], 'oxkeywords', null, $key);
			$desc = $seoEncoder->getMetaData($data['OXID'], 'oxdescription', null, $key);

			if (!empty($data['OXSHORTDESC'.$column]) || !empty($keywords) || !empty($desc)) {
				$i18n = new ManufacturerI18nModel();
				$i18n->setDescription($data['OXSHORTDESC'.$column]);
				$i18n->setLanguageISO($language->iso3);
				$i18n->setmanufacturerId($model->getId());

				if ($keywords) $i18n->setMetaKeywords($keywords);
				if ($desc) $i18n->setMetaDescription($desc);

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
				$model->addFieldName('oxshortdesc'.$column);

				$model->assign(array(
					'oxtitle'.$column => $data->getName(),
					'oxshortdesc'.$column => $i18n->getDescription()					
				));
			}
		}
	}
}
