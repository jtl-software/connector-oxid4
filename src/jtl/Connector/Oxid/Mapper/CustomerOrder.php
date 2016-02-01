<?php
namespace jtl\Connector\Oxid\Mapper;

class CustomerOrder extends BaseMapper {
    protected $pull = array(
        'id' => 'OXID',
        'customerId' => 'OXUSERID',
        'billingAddress' => 'CustomerOrderBillingAddress',
        'creationDate' => 'OXORDERDATE',
        'currencyIso' => 'OXCURRENCY',
        'languageISO' => null,
        'note' => 'OXREMARK',
        'orderNumber' => 'OXORDERNR',
        'paymentDate' => 'payDate',
        'paymentModuleCode' => 'OXPAYMENTSID',
        'shippingAddress' => 'CustomerOrderShippingAddress',
        'shippingDate' => 'OXSENDDATE',
        'shippingInfo' => 'OXDELADDINFO',
        'shippingMethodName' => 'delName',
        'status' => null,
        'totalSum' => 'OXTOTALORDERSUM',
        'items' => 'CustomerOrderItem'
    );

    protected function languageISO($data)
    {
        $languages = $this->utils->getLanguages();

        if (isset($languages[$data['OXLANG']])) {
            return $languages[$data['OXLANG']]->iso3;
        }
    }

    protected function status($data)
    {
        switch ($data['OXFOLDER']) {
            case 'ORDERFOLDER_NEW':
                return 'new';
                break;
            case 'ORDERFOLDER_FINISHED':
                return 'completed';
                break;
        }
    }
}
