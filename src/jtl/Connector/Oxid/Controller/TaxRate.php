<?php
namespace jtl\Connector\Oxid\Controller;

class TaxRate extends BaseController
{
    public function pullData($data, $model, $limit = null)
    {
        $return = array();
        $rates = array();

        $oxConfig = \oxRegistry::getConfig();

        $rates[] = $oxConfig->getConfigParam('dDefaultVAT');

        $additionalRates = $this->db->getAll('SELECT OXVAT FROM oxarticles WHERE OXVAT IS NOT NULL GROUP BY OXVAT');

        foreach ($additionalRates as $aRate) {
            $rates[] = $aRate['OXVAT'];
        }

        foreach ($rates as $rate) {
            $model = $this->mapper->toHost(array('rate' => $rate));

            $return[] = $model;
        }

        return $return;
    }
}
