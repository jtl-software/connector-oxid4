<?php
namespace jtl\Connector\Oxid\Controller;

use jtl\Connector\Model\ProductSpecialPrice as ProductSpecialPriceModel;

class ProductSpecialPrice extends BaseController {
    public function pullData($data, $model, $limit = null)
    {
        $specialsResult = $this->db->getAll('
            SELECT o.*, d.*, a.OXPRICE FROM oxobject2discount o
            LEFT JOIN oxdiscount d ON d.OXID=o.OXDISCOUNTID
            LEFT JOIN oxarticles a ON a.OXID=o.OXOBJECTID
			WHERE o.OXOBJECTID = "'.$data['OXID'].'" && d.OXADDSUMTYPE = "abs"
		');

        $specials = array();

        foreach ($specialsResult as $specialData) {
            $model = $this->mapper->toHost($specialData);

            $specials[] = $model;;
        }

        return $specials;
    }

    /*
    public function pushData($data)
    {
        foreach ($data->getSpecialPrices() as $specialPrice) {
            foreach ($specialPrice->getItems() as $item) {
                if ($item->getCustomerGroupId()->getEndpoint() === 'oxidcustomer') {
                    $itemD = new \stdClass();
                    $itemD->OXID = $this->utils->oxid();
                    $itemD->OXSHOPID = 'oxbaseshop';
                    $itemD->OXACTIVEFROM = $specialPrice->getActiveFromDate() ? $specialPrice->getActiveFromDate()->format('Y-m-d H:i:s') : '0000-00-00 00:00:00';
                    $itemD->OXACTIVETO = $specialPrice->getActiveUntilDate() ? $specialPrice->getActiveUntilDate()->format('Y-m-d H:i:s') : '0000-00-00 00:00:00';
                    $itemD->OXTITLE = $itemD->OXID;
                    $itemD->OXADDSUMTYPE = 'abs';
                }
            }
        }
    }
    */
}
