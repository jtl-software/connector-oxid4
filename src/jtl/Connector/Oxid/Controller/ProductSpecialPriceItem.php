<?php
namespace jtl\Connector\Oxid\Controller;

use \jtl\Connector\Model\ProductSpecialPriceItem as ProductSpecialPriceItemModel;

class ProductSpecialPriceItem extends BaseController {
    public function pullData($data, $model, $limit = null)
    {
        $item = new ProductSpecialPriceItemModel();
        $item->setProductSpecialpriceId($model->getId());
        $item->setPriceNet(floatval($data['OXPRICE'] - $data['OXADDSUM']));

        return array($item);
    }
}