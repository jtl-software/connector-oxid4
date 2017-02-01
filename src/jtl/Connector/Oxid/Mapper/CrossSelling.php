<?php
namespace jtl\Connector\Oxid\Mapper;

class CrossSelling extends BaseMapper {
    protected $pull = array(
        'id' => 'OXARTICLENID',
        'productId' => 'OXARTICLENID',
        'items' => 'CrossSellingItem'
    );
}
