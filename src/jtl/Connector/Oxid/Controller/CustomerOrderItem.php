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
        $delivery->setPrice(round($data['OXDELCOST'] / (($data['OXDELVAT'] / 100) + 1 ), 2));
        $delivery->setPriceGross((float) $data['OXDELCOST']);
        $delivery->setQuantity(1);
        $delivery->setVat(floatval($data['OXDELVAT']));

        $return[] = $delivery;

        if (floatval($data['OXPAYCOST']) > 0) {
            $payment = new CustomerOrderItemModel();
            $payment->setType('product');
            $payment->setName($data['payType']);
            $payment->setPrice(round($data['OXPAYCOST'] / (($data['OXPAYVAT'] / 100) + 1 ), 2));
            $payment->setPriceGross((float) $data['OXPAYCOST']);
            $payment->setQuantity(1);
            $payment->setVat(floatval($data['OXPAYVAT']));

            $return[] = $payment;
        }

        if (floatval($data['OXVOUCHERDISCOUNT']) > 0) {
            $discount = new CustomerOrderItemModel();
            $discount->setType('product');
            $discount->setName('Gutschein Rabatt');
            $discount->setPrice(round($data['OXVOUCHERDISCOUNT'] * -1, 2));
            $discount->setPriceGross(round($data['OXVOUCHERDISCOUNT'] * -1, 2));
            $discount->setQuantity(1);
            $discount->setVat(0);

            $return[] = $discount;
        }

        if (floatval($data['OXGIFTCARDCOST']) > 0) {
            $card = new CustomerOrderItemModel();
            $card->setType('product');
            $card->setName('Grußkarte: '.$data['OXCARDTEXT']);
            $card->setPrice(round($data['OXGIFTCARDCOST'] / (($data['OXGIFTCARDVAT'] / 100) + 1 ), 2));
            $card->setPriceGross((float) $data['OXGIFTCARDCOST']);
            $card->setQuantity(1);
            $card->setVat(floatval($data['OXGIFTCARDVAT']));

            $return[] = $card;
        }

        return $return;
    }
}
