<?php
namespace jtl\Connector\Oxid\Mapper;

class Payment extends BaseMapper
{
    protected $endpointModel = '\oxUserPayment';

    protected $pull = array(
        'id' => 'OXID',
        'customerOrderId' => 'orderId',
        'creationDate' => 'OXTIMESTAMP',
        'paymentModuleCode' => 'OXPAYMENTSID'
    );

    protected $push = array(
        'OXID' => 'id',
        'OXTIMESTAMP' => 'creationDate',
        'OXPAYMENTSID' => 'paymentModuleCode',
        'OXUSERID' => null
    );

    protected function OXUSERID($data, $userId)
    {
        return $userId;
    }
}
