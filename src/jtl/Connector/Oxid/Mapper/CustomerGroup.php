<?php
namespace jtl\Connector\Oxid\Mapper;

class CustomerGroup extends BaseMapper {
    protected $pull = array(
        'id' => 'OXID',
        'isDefault' => null,
        'i18ns' => 'CustomerGroupI18n'
    );

    protected function isDefault($data)
    {
        return $data['OXID'] === 'oxidcustomer' ? true : false;
    }
}
