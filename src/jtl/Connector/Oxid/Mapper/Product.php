<?php
namespace jtl\Connector\Oxid\Mapper;

use \jtl\Connector\Model\ProductStockLevel;
use \jtl\Connector\Model\Identity;

class Product extends BaseMapper
{
	protected $endpointModel = '\oxArticle';

	protected $pull = array(
		'id' => 'OXID',
		'manufacturerId' => 'OXMANUFACTURERID',
		'masterProductId' => 'OXPARENTID',
		'measurementUnitId' => 'OXUNITNAME',
		'creationDate' => 'OXINSERT',
		'ean' => 'OXEAN',
		'height' => 'OXHEIGHT',
		'isActive' => 'OXACTIVE',
        'isMasterProduct' => null,
		'keywords' => 'OXSEARCHKEYS',
		'length' => 'OXLENGTH',
		'manufacturerNumber' => 'OXMPN',
		'measurementQuantity' => 'OXUNITQUANTITY',
		'measurementUnitCode' => null,
		'modified' => 'OXTIMESTAMP',
		'nextAvailableInflowDate' => 'OXDELIVERY',
		'productWeight' => 'OXWEIGHT',
		'sku' => 'OXARTNUM',
		'sort' => 'OXSORT',
		'stockLevel' => null,
		'vat' => null,
		'width' => 'OXWIDTH',
		'attributes' => 'ProductAttr',
		'categories' => 'Product2Category',
		'i18ns' => 'ProductI18n',
		'prices' => 'ProductPrice',
		'specialPrices' => 'ProductSpecialPrice'
		//'varCombinations'
		//'partsLists'
		//'partsListId'		
	);

	protected $push = array(
		'OXID' => 'id'
	);

    protected function isMasterProduct($data)
    {
        return $data['combis'] > 0;
    }

	protected function measurementUnitCode($data)
	{
		return substr(strrchr($data['OXUNITNAME'], "_"), 1);
	}

	protected function stockLevel($data)
	{
		$stock = new ProductStockLevel();
		$stock->setProductId(new Identity($data['OXID']));
		$stock->setStockLevel(floatval($data['OXSTOCK']));

		return $stock;
	}

	protected function vat($data)
	{
		if (is_null($data['OXVAT'])) {
			return floatval(\oxRegistry::getConfig()->getConfigParam('dDefaultVAT'));
		}

		return floatval($data['OXVAT']);
	}
}
