<?php
namespace jtl\Connector\Oxid\Controller;

use \jtl\Connector\Model\CrossSellingItem as CrossSellingItemModel;
use jtl\Connector\Model\Identity;

class CrossSellingItem extends BaseController {
    public function pullData($data, $model, $limit = null)
    {
        $result = $this->db->getAll('
            SELECT c.OXOBJECTID
			FROM oxobject2article c
			WHERE c.OXARTICLENID = "'.$data['OXARTICLENID'].'"'
        );

        $model = new CrossSellingItemModel();
        $model->setCrossSellingGroupId(new Identity('default'));

        foreach ($result as $iData) {
            $model->addProductId(new Identity($iData['OXOBJECTID']));
        }

        return array($model);
    }
}
