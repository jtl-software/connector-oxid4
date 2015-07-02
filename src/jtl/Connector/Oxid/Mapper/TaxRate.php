<?php
namespace jtl\Connector\Oxid\Mapper;

class TaxRate extends BaseMapper
{
    protected $pull = array(
        'id' => 'rate',
        'rate' => 'rate'
    );
}
