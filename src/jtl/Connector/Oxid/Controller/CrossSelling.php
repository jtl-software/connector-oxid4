<?php
namespace jtl\Connector\Oxid\Controller;

class CrossSelling extends BaseController {
    public function pullData($data, $model, $limit = null)
    {
        $result = $this->db->getAll('
            SELECT c.*
			FROM oxobject2article c
			LEFT JOIN jtl_connector_link l ON c.OXARTICLENID = l.endpointId AND l.type = 1024
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

    public function pushData($data)
    {
        $id = $data->getProductId()->getEndpoint();

        if (!is_null($id)) {
            $this->deleteData($data);

            foreach ($data->getItems() as $item) {
                foreach ($item->getProductIds() as $product) {
                    $obj = new \stdClass();
                    $obj->OXID = $this->utils->oxid();
                    $obj->OXOBJECTID = $product->getEndpoint();
                    $obj->OXARTICLENID = $id;

                    $this->db->insert($obj, 'oxobject2article');
                }
            }
        }

        return $data;
    }

    public function deleteData($data)
    {
        $id = $data->getProductId()->getEndpoint();

        if (!is_null($id)) {
            $this->db->execute('DELETE FROM oxobject2article WHERE OXARTICLENID="'.$id.'"');
        }

        return $data;
    }
}
