<?php
namespace jtl\Connector\Oxid\Controller;

use \jtl\Connector\Model\CustomerOrderItem as CustomerOrderItemModel;

class CustomerOrderItem extends BaseController {
    public function pullData($data, $model, $limit = null)
    {
        $result = $this->db->getAll('
			SELECT a.*
			FROM oxorderarticles a
			WHERE a.OXORDERID = "'.$data['OXID'].'"'
        );

        $return = array();

        foreach ($result as $aData) {
            $model = $this->mapper->toHost($aData);

            $return[] = $model;
        }

        $delivery = new CustomerOrderItemModel();
        $delivery->setType('shipping');
        $delivery->setName($data['delName']);
        $delivery->setPrice($data['OXDELCOST'] - ($data['OXDELCOST'] / 100 * $data['OXDELVAT']));
        $delivery->setQuantity(1);
        $delivery->setVat(floatval($data['OXDELVAT']));

        $return[] = $delivery;

        return $return;
    }
}
