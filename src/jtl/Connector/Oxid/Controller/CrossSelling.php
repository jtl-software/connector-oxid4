<?php
namespace jtl\Connector\Oxid\Controller;

class CrossSelling extends BaseController {
    public function pullData($data, $model, $limit = null)
    {
        $result = $this->db->getAll('
            SELECT c.*
			FROM oxobject2article c
			LEFT JOIN jtl_connector_link l ON c.OXID = l.endpointId AND l.type = 1024
            WHERE l.hostId IS NULL
            GROUP BY c.OXARTICLENID
            LIMIT '.$limit
        );

        $return = array();

        foreach ($result as $cData) {
            $model = $this->mapper->toHost($cData);

            $return[] = $model;
        }

        return $return;
    }
}
