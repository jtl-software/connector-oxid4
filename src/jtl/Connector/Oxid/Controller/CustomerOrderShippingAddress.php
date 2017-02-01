<?php
namespace jtl\Connector\Oxid\Controller;

use \jtl\Connector\Model\CustomerOrderShippingAddress as CustomerOrderShippingAddressModel;

class CustomerOrderShippingAddress extends BaseController {
    public function pullData($data, $model, $limit = null)
    {
        $return = new CustomerOrderShippingAddressModel();
        $return->setCustomerId($model->getCustomerId());
        $return->setCity($data['OXDELCITY']);
        $return->setCompany($data['OXDELCOMPANY']);
        $return->setCountryIso($this->utils->getCountryIso($data['OXDELCOUNTRYID']));
        $return->setDeliveryInstruction($data['OXDELADDINFO']);
        $return->setEMail($data['OXDELEMAIL']);
        $return->setFax($data['OXDELFAX']);
        $return->setFirstName($data['OXDELFNAME']);
        $return->setLastName($data['OXDELLNAME']);
        $return->setPhone($data['OXDELFON']);
        $return->setSalutation($data['OXDELSAL'] == 'MR' ? 'm' : 'w');
        $return->setState($this->utils->getState($data['OXDELSTATEID']));
        $return->setStreet($data['OXDELSTREET'].' '.$data['OXDELSTREETNR']);
        $return->setZipCode($data['OXDELZIP']);

        return $return;
    }
}
