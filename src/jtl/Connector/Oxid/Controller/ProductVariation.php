<?php
namespace jtl\Connector\Oxid\Controller;

use jtl\Connector\Model\Identity;
use \jtl\Connector\Model\ProductVariation as ProductVariationModel;
use \jtl\Connector\Model\ProductVariationValue as ProductVariationValueModel;
use \jtl\Connector\Model\ProductVariationI18n as ProductVariationI18nModel;
use \jtl\Connector\Model\ProductVariationValueI18n as ProductVariationValueI18nModel;

class ProductVariation extends BaseController
{
    private static $columns = 3;

    public function pullData($data, $model, $limit = null)
    {
        if ($model->getIsMasterProduct()) {
            $where = 'c.OXPARENTID="'.$data['OXID'].'"';
        } else {
            $where = 'c.OXID="'.$data['OXID'].'"';
        }

        $result = $this->db->getAll('
            SELECT c.OXVARSELECT, c.OXVARSELECT_1, c.OXVARSELECT_2, c.OXVARSELECT_3, a.OXVARNAME, a.OXVARNAME_1, a.OXVARNAME_2, a.OXVARNAME_3
            FROM oxarticles c
            LEFT JOIN oxarticles a ON a.OXID = c.OXPARENTID
            WHERE '.$where);

        $return = array();
        $variations = array();

        foreach ($result as $combi) {
            $varNames = explode(' | ', $combi['OXVARNAME']);
            $values = explode(' | ', $combi['OXVARSELECT']);

            $varCount = 0;
            foreach ($varNames as $name) {
                if (!in_array($values[$varCount], $variations[$name]['values'])) {
                    $variations[$name]['i18ns'][0] = $varNames[$varCount];
                    $variations[$name]['values'][$values[$varCount]] = array();
                    $variations[$name]['values'][$values[$varCount]]['i18ns'][0] = $values[$varCount];

                    for ($i = 1; $i < static::$columns; $i++) {
                        $column = '_' . $i;
                        $i18nNames = explode(' | ', $combi['OXVARNAME' . $column]);
                        if (!empty($i18nNames[$varCount])) {
                            $variations[$name]['i18ns'][$i] = $i18nNames[$varCount];
                        }

                        $i18nValues = explode(' | ', $combi['OXVARSELECT'.$column]);
                        if (!empty($i18nValues[$varCount])) {
                            $variations[$name]['values'][$values[$varCount]]['i18ns'][$i] = $i18nValues[$varCount];
                        }

                    }
                }

                $varCount++;
            }
        }

        foreach ($variations as $variationId => $variationData) {
            $varModel = new ProductVariationModel();
            $varModel->setId(new Identity($variationId));
            $varModel->setProductId($model->getId());

            foreach ($this->utils->getLanguages() as $langId => $language) {
                if (isset($variationData['i18ns'][$langId])) {
                    $varI18n = new ProductVariationI18nModel();
                    $varI18n->setProductVariationId($varModel->getId());
                    $varI18n->setName($variationData['i18ns'][$langId]);
                    $varI18n->setLanguageISO($language->iso3);
                    $varModel->addI18n($varI18n);
                }
            }

            foreach ($variationData['values'] as $valueId => $valueData) {
                $valueModel = new ProductVariationValueModel();
                $valueModel->setProductVariationId($varModel->getId());
                $valueModel->setId(new Identity($valueId));

                foreach ($this->utils->getLanguages() as $langId => $language) {
                    if (isset($valueData['i18ns'][$langId])) {
                        $valueI18n = new ProductVariationValueI18nModel();
                        $valueI18n->setProductVariationValueId($valueModel->getId());
                        $valueI18n->setName($valueData['i18ns'][$langId]);
                        $valueI18n->setLanguageISO($language->iso3);
                        $valueModel->addI18n($valueI18n);
                    }
                }

                $varModel->addValue($valueModel);
            }

            $return[] = $varModel;
        }

        return $return;
    }

    public function pushData($data, $model)
    {
        if ($data->getIsMasterProduct() === true) {
            $vars = array();

            foreach ($data->getVariations() as $variation) {
                foreach ($variation->getI18ns() as $i18n) {
                    $col = $this->utils->getLanguageId($i18n->getLanguageISO());
                    $vars[$col][] = $i18n->getName();
                }
            }

            foreach ($this->utils->getLanguages() as $language) {
                if (isset($vars[$language->column])) {
                    $varStr = implode(' | ', $vars[$language->column]);
                    $col = $language->column === 0 ? '' : '_' . $language->column;

                    $model->addFieldName('oxvarname' . $col);
                    $model->assign(array(
                        'oxvarname' . $col => $varStr
                    ));
                }
            }
        } else {
            $parent = $data->getMasterProductId()->getEndpoint();
            if (!empty($parent)) {
                $parentVars = $this->db->getOne('SELECT OXVARNAME FROM oxarticles WHERE OXID="'.$parent.'"');

                if ($parentVars) {
                    $parentVars = explode(' | ', $parentVars);
                    foreach ($data->getVariations() as $variation) {
                        foreach ($variation->getI18ns() as $varI18n) {
                            if ($this->utils->getLanguageId($varI18n->getLanguageISO()) === 0) {
                                $index = array_search($varI18n->getName(), $parentVars);
                                break;
                            }
                        }

                        if ($index !== false) {
                            foreach ($variation->getValues() as $value) {
                                foreach ($value->getI18ns() as $i18n) {
                                    $col = $this->utils->getLanguageId($i18n->getLanguageISO());
                                    $values[$col][$index] = $i18n->getName();
                                }
                            }
                        }

                    }

                    foreach ($values as $column => $valueData) {
                        ksort($valueData);

                        $valueStr = implode(' | ', $valueData);
                        $oCol = $column === 0 ? '' : '_' . $column;

                        $model->addFieldName('oxvarselect' . $oCol);
                        $model->assign(array(
                            'oxvarselect' . $oCol => $valueStr
                        ));
                    }
                }
            }
        }
    }
}
