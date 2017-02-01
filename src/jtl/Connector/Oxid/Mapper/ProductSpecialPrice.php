<?php
namespace jtl\Connector\Oxid\Mapper;

class ProductSpecialPrice extends BaseMapper {
    protected $pull = array(
        'id' => 'OXID',
        'productId' => 'OXOBJECTID',
        'activeFromDate' => 'OXACTIVEFROM',
        'activeUntilDate' => 'OXACTIVETO',
        'considerDateLimit' => null,
        'isActive' => null,
        'items' => 'ProductSpecialPriceItem'
    );

    protected function considerDateLimit($data)
    {
        return true;
    }

    protected function isActive($data)
    {
        return true;
    }
}