<?php
namespace jtl\Connector\Oxid\Controller;

class ShippingMethod extends BaseController
{
    public function pullData($data, $model, $limit = null)
    {
        $result = $this->db->getAll('
			SELECT d.*
			FROM oxdeliveryset d
        ');

        $return = array();

        foreach ($result as $data) {
            $model = $this->mapper->toHost($data);

            $return[] = $model;
        }

        return $return;
    }
}
