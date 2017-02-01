<?php
namespace jtl\Connector\Oxid\Controller;

class StatusChange extends BaseController {
    private static $orderMapping = array(
        'new' => 'ORDERFOLDER_NEW',
        'shipped' => 'ORDERFOLDER_FINISHED',
        'partially_shipped' => 'ORDERFOLDER_FINISHED',
        'completed' => 'ORDERFOLDER_FINISHED',
        'cancelled' => 'ORDERFOLDER_PROBLEMS'
    );

    public function pushData($data)
    {
        $id = $data->getCustomerOrderId()->getEndpoint();

        if (!is_null($id)) {
            $orderStatus = $data->getOrderStatus();
            $paymentStatus = $data->getPaymentStatus();

            if (!empty($orderStatus)) {
                if (isset(static::$orderMapping[$orderStatus])) {
                    $this->db->execute('UPDATE oxorder SET OXFOLDER="'.static::$orderMapping[$orderStatus].'" WHERE OXID="'.$id.'"');
                }
            }

            if (!empty($paymentStatus)) {
                if ($paymentStatus == 'unpaid') {
                    $this->db->execute('DELETE p FROM oxuserpayments p LEFT JOIN oxorder o ON o.OXPAYMENTID = p.OXID WHERE o.OXID = "'.$id.'"');
                    $this->db->execute('UPDATE oxorder SET OXPAYMENTID = NULL, OXPAID = "0000-00-00 00:00:00", OXPAYMENTTYPE = NULL WHERE OXID="'.$id.'"');
                }
            }
        }

        return $data;
    }
}
