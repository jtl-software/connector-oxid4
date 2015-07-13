<?php
namespace jtl\Connector\Oxid\Controller;

use \jtl\Connector\Model\CustomerOrderBillingAddress as CustomerOrderBillingAddressModel;

class CustomerOrderBillingAddress extends BaseController {
    public function pullData($data, $model, $limit = null)
    {
        $return = new CustomerOrderBillingAddressModel();
        $return->setCustomerId($model->getCustomerId());
        $return->setCity($data['OXBILLCITY']);
        $return->setCompany($data['OXBILLCOMPANY']);
        $return->setCountryIso($this->utils->getCountryIso($data['OXBILLCOUNTRYID']));
        $return->setDeliveryInstruction($data['OXBILLADDINFO']);
        $return->setEMail($data['OXBILLEMAIL']);
        $return->setFax($data['OXBILLFAX']);
        $return->setFirstName($data['OXBILLFNAME']);
        $return->setLastName($data['OXBILLLNAME']);
        $return->setPhone($data['OXBILLFON']);
        $return->setSalutation($data['OXBILLSAL'] == 'MR' ? 'm' : 'w');
        $return->setState($this->utils->getState($data['OXBILLSTATEID']));
        $return->setStreet($data['OXBILLSTREET'].' '.$data['OXBILLSTREETNR']);
        $return->setZipCode($data['OXBILLZIP']);

        return $return;
    }
}
