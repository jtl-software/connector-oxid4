<?php
namespace jtl\Connector\Oxid\Controller;

class CustomerGroup extends BaseController {
    public function pullData($data, $model, $limit = null)
    {
        $result = $this->db->getAll('
			SELECT g.*
			FROM oxgroups g
			WHERE g.OXID LIKE "oxidprice%" OR g.OXID="oxidcustomer"
            ORDER BY g.OXTITLE
        ');

        $return = array();

        foreach ($result as $gData) {
            $model = $this->mapper->toHost($gData);

            $return[] = $model;
        }

        return $return;
    }

    public function pushData($data)
    {
        foreach ($data->getCustomerGroups() as $group) {
            $groupDiscounts = $this->db->getAll('
                SELECT OXDISCOUNTID FROM oxobject2discount WHERE OXOBJECTID="'.$group->getId()->getEndpoint().'"
            ');

            $this->db->execute('DELETE FROM oxobject2discount WHERE OXOBJECTID="'.$group->getId()->getEndpoint().'"');

            foreach ($groupDiscounts as $discount) {
                $this->db->execute('
                    DELETE d
                    FROM oxdiscount d
                    LEFT JOIN oxobject2discount r ON r.OXDISCOUNTID = d.OXID
                    WHERE r.OXDISCOUNTID IS NULL && d.OXID = "'.$discount['OXDISCOUNTID'].'"
                ');
            }

            if ($group->getDiscount() > 0) {
                $grpDiscount = new \stdClass();
                $grpDiscount->OXID = $this->utils->oxid();
                $grpDiscount->OXTITLE = $group->getId()->getEndpoint();
                $grpDiscount->OXADDSUMTYPE = '%';
                $grpDiscount->OXADDSUM = $group->getDiscount();
                $grpDiscount->OXACTIVE = 1;

                $this->db->insert($grpDiscount, 'oxdiscount');

                $grpRelation = new \stdClass();
                $grpRelation->OXID = $this->utils->oxid();
                $grpRelation->OXDISCOUNTID = $grpDiscount->OXID;
                $grpRelation->OXOBJECTID = $group->getId()->getEndpoint();
                $grpRelation->OXTYPE = 'oxgroups';

                $this->db->insert($grpRelation, 'oxobject2discount');
            }
        }

        return $data;
    }
}
