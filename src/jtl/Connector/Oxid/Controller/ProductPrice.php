<?php
namespace jtl\Connector\Oxid\Controller;

use \jtl\Connector\Model\ProductPrice as ProductPriceModel;
use \jtl\Connector\Model\ProductPriceItem as ProductPriceItemModel;
use \jtl\Connector\Model\Identity;

class ProductPrice extends BaseController
{	
	private static $groups = array(
        'A',
        'B',
        'C'
    );

    public function pullData($data, $model, $limit = null)
	{
		$prices = array();

        $result = $this->db->getAll('SELECT * FROM oxprice2article WHERE OXARTID="'.$data['OXID'].'"');

		$price = new ProductPriceModel();
		$price->setProductId($model->getId());
		$price->setId(new Identity($model->getId()->getEndpoint().'_default'));
        $price->setCustomerGroupId(new Identity('oxidcustomer'));

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

        $prices[] = $price;

        foreach (static::$groups as $group) {
            if (isset($data['OXPRICE'.$group]) && $data['OXPRICE'.$group] > 0) {
                $groupPrice = new ProductPriceModel();
                $groupPrice->setCustomerGroupId(new Identity('oxidprice'.strtolower($group)));
                $groupPrice->setId(new Identity($data['OXID'].'_'.$group));
                $groupPrice->setProductId(new Identity($data['OXID']));

                $groupPriceItem = new ProductPriceItemModel();
                $groupPriceItem->setProductPriceId($groupPrice->getId());
                $groupPriceItem->setNetPrice(floatval($data['OXPRICE'.$group]));

                $groupPrice->addItem($groupPriceItem);

                $prices[] = $groupPrice;
            }
        }

		return $prices;
	}

    public function pushData($data)
    {
        foreach ($data as $price) {
            $group = $data->getCustomerGroupId()->getEndpoint();
        }

        //return $data;
    }
}
