<?php
namespace jtl\Connector\Oxid\Controller;

class ProductStockLevel extends BaseController {
    public function pushData($data)
    {
        if (get_class($data) == 'jtl\Connector\Model\Product') {
            $data = $data->getStockLevel();
        }

        $productId = $data->getProductId()->getEndpoint();

        if (!empty($productId)) {
            $this->db->execute('UPDATE oxarticles SET OXSTOCK=' . $data->getStockLevel() . ' WHERE OXID="' . $productId . '"');
        }

        return $data;
    }
}
