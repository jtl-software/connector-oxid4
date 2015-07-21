<?php
namespace jtl\Connector\Oxid\Controller;

use \jtl\Connector\Model\ProductI18n as ProductI18nModel;

class ProductI18n extends BaseController
{	
	public function pullData($data, $model)
	{
		$i18ns = array();

		$seoEncoder = new \oxSeoEncoder();

		foreach ($this->utils->getLanguages() as $id => $language) {
			$column = $id == 0 ? '' : '_'.$id;
			
			if (!empty($data['OXTITLE'.$column])) {
				$i18n = new ProductI18nModel();
				$i18n->setName($data['OXTITLE'.$column]);
				$i18n->setDescription($data['OXLONGDESC'.$column]);
				$i18n->setShortDescription($data['OXSHORTDESC'.$column]);
				$i18n->setMetaKeywords($data['OXTAGS'.$column]);
				$i18n->setLanguageISO($language->iso3);
				$i18n->setProductId($model->getId());
				$i18n->setMeasurementUnitName($data['OXUNITNAME']);
				
				$metaKeys = $seoEncoder->getMetaData($data['OXID'], 'oxkeywords', null, $id);
				if ($metaKeys) {
					$i18n->setMetaKeywords($metaKeys);
				}
				
				$metaDesc = $seoEncoder->getMetaData($data['OXID'], 'oxdescription', null, $id);
				if ($metaDesc) {
					$i18n->setMetaDescription($metaDesc);
				}				
				
				$i18ns[] = $i18n;
			}
		}

		return $i18ns;			
	}
	
	public function pushData($data, $model)
	{
        $extend = \oxNew('oxI18n');
        $extend->setEnableMultilang(false);
        $extend->init('oxartextends');
        $extend->setId($data->getId()->getEndpoint());

        foreach ($data->getI18ns() as $i18n) {
			$id = $this->utils->getLanguageId($i18n->getLanguageISO());
			
			if ($id !== false) {
				$column = $id == 0 ? '' : '_'.$id;

				$model->addFieldName('oxtitle'.$column);
				$model->addFieldName('oxshortdesc'.$column);
                $model->addFieldName('oxsearchkeys'.$column);

				$model->assign(array(
					'oxtitle'.$column => $i18n->getName(),
					'oxshortdesc'.$column => $i18n->getShortDescription(),
                    'oxsearchkeys'.$column => $data->getKeywords()
				));

                $extend->{oxartextends__oxlongdesc.$column} = new \oxField($i18n->getDescription());
                $extend->{oxartextends__oxtags.$column} = new \oxField($data->getKeywords());

                $seo = new \stdClass();
                $seo->OXOBJECTID = $data->getId()->getEndpoint();
                $seo->OXSHOPID = 'oxbaseshop';
                $seo->OXLANG = $id;
                $seo->OXKEYWORDS = $i18n->getMetaKeywords();
                $seo->OXDESCRIPTION = $i18n->getMetaDescription();

                $this->db->insert($seo, 'oxobject2seodata');
			}
		}

        $extend->save();
	}	
}
