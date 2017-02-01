<?php
namespace jtl\Connector\Oxid\Controller;

class DeliveryNote extends BaseController
{
    public function pushData($data)
    {
        $orderId = $data->getCustomerOrderId()->getEndpoint();

        if (!empty($orderId)) {
            foreach ($data->getTrackingLists() as $list) {
                $this->db->execute('UPDATE oxorder SET OXTRACKCODE="'.implode(', ', $list->getCodes()).'" WHERE OXID="'.$orderId.'"');
            }
        }

        return $data;
    }
}
