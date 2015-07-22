<?php
namespace jtl\Connector\Oxid\Controller;

use jtl\Connector\Model\Identity;
use \jtl\Connector\Model\ProductSpecialPriceItem as ProductSpecialPriceItemModel;

class ProductSpecialPriceItem extends BaseController {
    public function pullData($data, $model, $limit = null)
    {
        $item = new ProductSpecialPriceItemModel();
        $item->setProductSpecialpriceId($model->getId());
        $item->setPriceNet(floatval($data['OXPRICE'] - $data['OXADDSUM']));
        $item->setCustomerGroupId(new Identity('oxidcustomer'));

        return array($item);
    }
}