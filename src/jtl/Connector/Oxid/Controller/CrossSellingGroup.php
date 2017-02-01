<?php
namespace jtl\Connector\Oxid\Controller;

use \jtl\Connector\Model\CrossSellingGroup as CrossSellingGroupModel;
use \jtl\Connector\Model\CrossSellingGroupI18n as CrossSellingGroupI18nModel;
use jtl\Connector\Model\Identity;

class CrossSellingGroup extends BaseController
{
    public function pullData($data, $model, $limit = null)
    {
        $defaultGrp = new CrossSellingGroupModel();
        $defaultGrp->setId(new Identity('default'));

        foreach ($this->utils->getLanguages() as $column => $language) {
            $i18n = new CrossSellingGroupI18nModel();
            $i18n->setName('Oxid');
            $i18n->setDescription('Oxid Standard Crossselling Gruppe');
            $i18n->setLanguageISO($language->iso3);
            $i18n->setCrossSellingGroupId($defaultGrp->getId());

            $defaultGrp->addI18n($i18n);
        }

        return array($defaultGrp);
    }
}
