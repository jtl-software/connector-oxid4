<?php
namespace jtl\Connector\Oxid\Mapper;

class CustomerGroup extends BaseMapper {
    protected $pull = array(
        'id' => 'OXID',
        'isDefault' => null,
        'discount' => null,
        'i18ns' => 'CustomerGroupI18n'
    );

    protected function isDefault($data)
    {
        return $data['OXID'] === 'oxidcustomer' ? true : false;
    }

    protected function discount($data)
    {
        $discount = $this->db->getOne('
            SELECT
                d.OXADDSUM,
                (
                    SELECT COUNT(*)
                    FROM oxobject2discount
                    WHERE OXDISCOUNTID = r.OXDISCOUNTID
                ) AS relationCount
            FROM oxobject2discount r
            LEFT JOIN oxdiscount d ON d.OXID = r.OXDISCOUNTID
            WHERE r.OXOBJECTID = "'.$data['OXID'].'" && d.OXADDSUMTYPE="%" && d.OXACTIVE = 1 && d.OXADDSUM > 0
            HAVING relationCount = 1
        ');

        if ($discount) {
            return floatval($discount);
        }
    }
}
