<?php
namespace jtl\Connector\Oxid\Mapper;

class CrossSelling extends BaseMapper {
    protected $pull = array(
        'productId' => 'OXARTICLENID',
        'items' => 'CrossSellingItem'
    );
}
