<?php
namespace jtl\Connector\Oxid\Controller;

class ProductStockLevel extends BaseController {
    public function pushData($data)
    {
        $productId = $data->getProductId()->getEndpoint();

        if (!empty($productId)) {
            $this->db->execute('UPDATE oxarticles SET OXSTOCK='.$data->getStockLevel().' WHERE OXID="'.$productId.'"');
        }

        return $data;
    }
}
