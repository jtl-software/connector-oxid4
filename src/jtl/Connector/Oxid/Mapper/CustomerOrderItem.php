<?php
namespace jtl\Connector\Oxid\Mapper;

class CustomerOrderItem extends BaseMapper {
    protected $pull = array(
        'id' => 'OXID',
        'customerOrderId' => 'OXORDERID',
        'productId' => 'OXARTID',
        'name' => 'OXTITLE',
        'price' => 'OXNPRICE',
        'priceGross' => 'OXBPRICE',
        'quantity' => 'OXAMOUNT',
        'sku' => 'OXARTNUM',
        'type' => null,
        'vat' => 'OXVAT'
    );

    protected function type($data)
    {
        return 'product';
    }
}
