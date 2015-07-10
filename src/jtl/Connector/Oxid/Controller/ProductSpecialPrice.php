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
}
