<?php
namespace jtl\Connector\Oxid\Controller;

use \jtl\Connector\Model\CategoryI18n as CategoryI18nModel;

class CategoryI18n extends BaseController
{	
	public function pullData($data, $model)
	{
		$i18ns = array();

		$seoEncoder = new \oxSeoEncoder();

		foreach ($this->utils->getLanguages() as $id => $language) {
			$column = $id == 0 ? '' : '_'.$id;

			if (!empty($data['OXTITLE'.$column])) {
				$i18n = new CategoryI18nModel();
				$i18n->setName($data['OXTITLE'.$column]);
				$i18n->setDescription($data['OXLONGDESC'.$column]);
				$i18n->setLanguageISO($language->iso3);
				$i18n->setCategoryId($model->getId());
				
				$metaKeys = $seoEncoder->getMetaData($data['OXID'], 'oxkeywords', null, $id);
				if ($metaKeys) {
					$i18n->setMetaKeywords($metaKeys);
				}
				
				$metaDesc = $seoEncoder->getMetaData($data['OXID'], 'oxdescription', null, $id);
				if ($metaDesc) {
					$i18n->setMetaDescription($metaDesc);
				}

				$seoUrl = $this->db->getOne('SELECT OXSEOURL FROM oxseo WHERE OXOBJECTID="'.$data['OXID'].'" && OXTYPE="oxcategory" && OXLANG='.$id);
				if ($seoUrl) {
					$i18n->setUrlPath($seoUrl);
				}

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
