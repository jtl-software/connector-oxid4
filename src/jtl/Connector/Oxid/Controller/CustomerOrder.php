<?php
namespace jtl\Connector\Oxid\Controller;

class CustomerOrder extends BaseController {
    public function pullData($data, $model, $limit = null)
    {
        $result = $this->db->getAll('
			SELECT o.*, p.OXPAYMENTSID, p.OXTIMESTAMP AS payDate, d.OXTITLE AS delName
			FROM oxorder o
			LEFT JOIN oxuserpayments p ON p.OXID = o.OXPAYMENTID
			LEFT JOIN oxdeliveryset d ON d.OXID = o.OXDELTYPE
			LEFT JOIN jtl_connector_link l ON o.OXID = l.endpointId AND l.type = 4
            WHERE l.hostId IS NULL
            LIMIT '.$limit
        );

        $return = array();

        foreach ($result as $oData) {
            $model = $this->mapper->toHost($oData);

            $return[] = $model;
        }

        return $return;
    }

    public function getStats()
    {
        return $this->db->GetOne('
			SELECT COUNT(*)
			FROM oxorder o
			LEFT JOIN jtl_connector_link l ON o.OXID = l.endpointId AND l.type = 4
            WHERE l.hostId IS NULL
        ');
    }
}
