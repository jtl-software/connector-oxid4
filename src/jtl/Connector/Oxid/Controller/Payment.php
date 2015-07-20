<?php
namespace jtl\Connector\Oxid\Controller;

class Payment extends BaseController
{
    public function pullData($data, $model, $limit = null)
    {
        $result = $this->db->getAll('
			SELECT p.*, '.$this->utils->decode('OXVALUE').' AS value, o.OXID AS orderId
			FROM oxuserpayments p
			LEFT JOIN oxorder o ON o.OXPAYMENTID = p.OXID
			LEFT JOIN jtl_connector_link l ON p.OXID = l.endpointId AND l.type = 512
            WHERE l.hostId IS NULL
            LIMIT '.$limit
        );

        $return = array();

        foreach ($result as $data) {
            $model = $this->mapper->toHost($data);

            $return[] = $model;
        }

        return $return;
    }

    public function pushData($data)
    {
        $orderId = $data->getCustomerOrderId()->getEndpoint();
        $userId = $this->db->GetOne('SELECT OXUSERID FROM oxorder WHERE OXID="'.$orderId.'"');

        if (!empty($userId)) {
            $payment = $this->mapper->toEndpoint($data, $userId);
            $payment->setUseSkipSaveFields(false);

            $id = $payment->save();

            if (!empty($id)) {
                $order = new \stdClass();
                $order->OXPAYMENTID = $id;
                $order->OXPAYMENTTYPE = $data->getPaymentModuleCode();
                $order->OXPAID = $data->getCreationDate()->format('Y-m-d H:i:s');

                $this->db->update($order, 'oxorder', 'OXID', $orderId);
            }

            $data->getId()->setEndpoint($id);
        }

        return $data;
    }

    public function getStats()
    {
        return $this->db->GetOne('
			SELECT COUNT(*)
			FROM oxuserpayments p
			LEFT JOIN jtl_connector_link l ON p.OXID = l.endpointId AND l.type = 512
            WHERE l.hostId IS NULL
        ');
    }
}
