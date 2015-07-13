<?php
namespace jtl\Connector\Oxid\Controller;

class ProductVarCombination extends BaseController {
    public function pullData($data, $model, $limit = null)
    {
        if ($model->getIsMasterProduct()) {
            $result = $this->db->getAll('
                SELECT OXID, OXPARENTID, OXVARSELECT
                FROM oxarticles
                WHERE OXPARENTID="'.$data['OXID'].'"'
            );

            $return = array();

            foreach ($result as $varData) {
                $values = explode(' | ', $varData['OXVARSELECT']);

                foreach ($values as $value) {
                    $varData['value'] = $value;

                    $model = $this->mapper->toHost($varData);

                    $return[] = $model;
                }
            }

            return $return;
        }
    }
}
