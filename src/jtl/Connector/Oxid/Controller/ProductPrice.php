<?php
namespace jtl\Connector\Oxid\Controller;

use \jtl\Connector\Model\ProductPrice as ProductPriceModel;
use \jtl\Connector\Model\ProductPriceItem as ProductPriceItemModel;
use \jtl\Connector\Model\Identity;

class ProductPrice extends BaseController
{	
	public function pullData($data, $model, $limit = null)
	{
		$result = $this->db->getAll('SELECT * FROM oxprice2article WHERE OXARTID="'.$data['OXID'].'"');

		$price = new ProductPriceModel();
		$price->setProductId($model->getId());
		$price->setId(new Identity($model->getId()->getEndpoint().'_default'));

		$items = array();

		$default = new ProductPriceItemModel();
		$default->setProductPriceId($price->getId());
		$default->setNetPrice(floatval($data['OXPRICE']));

		$items[] = $default;

		foreach ($result as $itemData) {
			$item = new ProductPriceItemModel();
			$item->setProductPriceId($price->getId());
			$item->setNetPrice(floatval($itemData['OXADDABS']));
			$item->setQuantity(intval($itemData['OXAMOUNT']));

			$items[] = $item;
		}

		$price->setItems($items);

		return array($price);
	}
}
