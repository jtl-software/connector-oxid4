<?php
namespace jtl\Connector\Oxid\Mapper;

class ShippingMethod extends BaseMapper
{
    protected $pull = array(
        'id' => 'OXID',
        'name' => 'OXTITLE'
    );
}
