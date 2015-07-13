<?php
namespace jtl\Connector\Oxid\Mapper;

class ProductVarCombination extends BaseMapper {
    protected $pull = array(
        'productVariationId' => 'OXID',
        'productId' => 'OXPARENTID',
        'productVariationValueId' => 'value'
    );
}
