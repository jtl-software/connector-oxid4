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
}
