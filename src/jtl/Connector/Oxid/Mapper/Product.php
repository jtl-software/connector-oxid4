<?php
namespace jtl\Connector\Oxid\Mapper;

use \jtl\Connector\Model\ProductStockLevel;
use \jtl\Connector\Model\Identity;

class Product extends BaseMapper
{
	protected $endpointModel = '\oxArticle';
	protected $id = 'id';

	protected $pull = array(
		'id' => 'OXID',
		'manufacturerId' => 'OXMANUFACTURERID',
		'masterProductId' => 'OXPARENTID',
		'measurementUnitId' => 'OXUNITNAME',
		'basePriceUnitId' => 'OXUNITNAME',
		'basePriceQuantity' => null,
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
		'basePriceUnitCode' => null,
        'basePriceUnitName' => null,
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
		//'specialPrices' => 'ProductSpecialPrice',
		'variations' => 'ProductVariation',
		'considerStock' => null,
		'permitNegativeStock' => null,
        'recommendedRetailPrice' => 'OXTPRICE'
	);

	protected $push = array(
		'OXID' => 'id',
		'OXMANUFACTURERID' => 'manufacturerId',
		'OXPARENTID' => 'masterProductId',
		'OXUNITNAME' => null,
		'OXINSERT' => 'creationDate',
		'OXEAN' => 'ean',
		'OXHEIGHT' => 'height',
		'OXACTIVE' => 'isActive',
		'OXLENGTH' => 'length',
		'OXMPN' => 'manufacturerNumber',
		'OXUNITQUANTITY' => 'measurementQuantity',
		'OXTIMESTAMP' => 'modified',
		'OXDELIVERY' => 'nextAvailableInflowDate',
		'OXWEIGHT' => 'productWeight',
		'OXARTNUM' => 'sku',
		'OXSORT' => 'sort',
		'ProductStockLevel' => 'stockLevel',
		'OXVAT' => null,
		'OXWIDTH' => 'width',
		'ProductI18n' => 'i18ns',
		//'ProductSpecialPrice' => 'specialPrices',
		'ProductVariation' => 'variations',
		'OXSHOPID' => null,
		'OXSTOCK' => null,
        'OXSTOCKFLAG' => null,
        'OXTPRICE' => 'recommendedRetailPrice'
	);

	protected function considerStock($data)
	{
		return \oxRegistry::getConfig()->getConfigParam('blUseStock');
	}

	protected function permitNegativeStock($data)
	{
		if ($data['OXSTOCKFLAG'] == 1) {
			return true;
		}

		return false;
	}

	protected function OXSTOCK($data)
	{
		return $data->getStockLevel()->getStockLevel();
	}

    protected function OXSTOCKFLAG($data)
    {
        return $data->getPermitNegativeStock() == true ? 1 : 3;
    }

	protected function OXSHOPID($data)
	{
		return 'oxbaseshop';
	}

    protected function OXUNITNAME($data)
    {
        return $data->getBasePriceUnitName();
    }

	protected function isMasterProduct($data)
    {
        return $data['combis'] > 0;
    }

	protected function measurementUnitCode($data)
	{
		return substr(strrchr($data['OXUNITNAME'], "_"), 1);
	}

	protected function basePriceQuantity($data)
	{
		return 1;
	}

	protected function basePriceUnitCode($data)
	{
		return substr(strrchr($data['OXUNITNAME'], "_"), 1);
	}

    protected function basePriceUnitName($data)
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

	protected function OXVAT($data)
	{
		if ($data->getVat() !== floatval(\oxRegistry::getConfig()->getConfigParam('dDefaultVAT'))) {
			return $data->getVat();
		}

		return null;
	}
}
