<?php
namespace jtl\Connector\Oxid\Controller;

use \jtl\Connector\Model\CustomerGroupI18n as CustomerGroupI18nModel;

class CustomerGroupI18n extends BaseController {
    public function pullData($data, $model)
    {
        $i18ns = array();

        foreach ($this->utils->getLanguages() as $id => $language) {
            $column = $id == 0 ? '' : '_'.$id;

            if (!empty($data['OXTITLE'.$column])) {
                $i18n = new CustomerGroupI18nModel();
                $i18n->setName($data['OXTITLE'.$column]);
                $i18n->setLanguageISO($language->iso3);
                $i18n->setCustomerGroupId($model->getId());

                $i18ns[] = $i18n;
            }
        }

        return $i18ns;
    }
}
