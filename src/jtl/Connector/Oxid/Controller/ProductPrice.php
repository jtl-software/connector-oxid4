<?php
namespace jtl\Connector\Oxid\Controller;

use \jtl\Connector\Model\ProductPrice as ProductPriceModel;
use \jtl\Connector\Model\ProductPriceItem as ProductPriceItemModel;
use \jtl\Connector\Model\Identity;

class ProductPrice extends BaseController
{	
	private static $groups = array(
        'A',
        'B',
        'C'
    );

    public function pullData($data, $model, $limit = null)
	{
        $prices = array();

        $result = $this->db->getAll('SELECT * FROM oxprice2article WHERE OXARTID="'.$data['OXID'].'"');

		$price = new ProductPriceModel();
		$price->setProductId($model->getId());
		$price->setId(new Identity($model->getId()->getEndpoint().'_default'));
        $price->setCustomerGroupId(new Identity('oxidcustomer'));

		$items = array();

		$default = new ProductPriceItemModel();
		$default->setProductPriceId($price->getId());
		$default->setNetPrice(floatval($data['OXPRICE']));

		$items[] = $default;

        foreach ($result as $itemData) {
			$item = new ProductPriceItemModel();
			$item->setProductPriceId($price->getId());
			$item->setQuantity(intval($itemData['OXAMOUNT']));

            if ($itemData['OXADDABS'] > 0 && $itemData['OXADDPERC'] == 0) {
                $item->setNetPrice(floatval($itemData['OXADDABS']));
            } elseif($itemData['OXADDPERC'] > 0 && $itemData['OXADDABS'] == 0) {
                $item->setNetPrice(floatval($data['OXPRICE']) - (floatval($data['OXPRICE']) / 100 * floatval($itemData['OXADDPERC'])));
            }

			$items[] = $item;
		}

		$price->setItems($items);

        $prices[] = $price;

        foreach (static::$groups as $group) {
            if (isset($data['OXPRICE'.$group]) && $data['OXPRICE'.$group] > 0) {
                $groupPrice = new ProductPriceModel();
                $groupPrice->setCustomerGroupId(new Identity('oxidprice'.strtolower($group)));
                $groupPrice->setId(new Identity($data['OXID'].'_'.$group));
                $groupPrice->setProductId(new Identity($data['OXID']));

                $groupPriceItem = new ProductPriceItemModel();
                $groupPriceItem->setProductPriceId($groupPrice->getId());
                $groupPriceItem->setNetPrice(floatval($data['OXPRICE'.$group]));

                $groupPrice->addItem($groupPriceItem);

                $prices[] = $groupPrice;
            }
        }

        $isNetPrices = \oxRegistry::getConfig()->getShopConfVar('blEnterNetPrice');

        if (!$isNetPrices) {
            foreach ($prices as $price) {
                foreach ($price->getItems() as &$item) {
                    $item->setNetPrice(floatval(($item->getNetprice() / (100 + $model->getVat())) * 100));
                }
            }
        }

		return $prices;
	}

    public function initPush($data) {
        $id = $data[0]->getProductId()->getEndpoint();

        if (!empty($id)) {
            $this->db->execute('DELETE FROM oxprice2article WHERE OXARTID="' . $id . '"');
            $this->db->execute('UPDATE oxarticles SET OXPRICEA=0, OXPRICEB=0,OXPRICEC=0 WHERE OXID="' . $id . '"');
        }
    }

    public function pushData($data)
    {
        if (get_class($data) === 'jtl\Connector\Model\Product') {
            foreach ($data->getPrices() as $priceData) {
                $this->pushPrice($priceData);
            }
        } else {
            $this->pushPrice($data);
        }
    }

    private function pushPrice($data)
    {
        $id = $data->getProductId()->getEndpoint();
        $mainPrice = null;

        if (!empty($id)) {
            $vat = $this->db->getOne('SELECT OXVAT FROM oxarticles WHERE OXID="'.$id.'"');

            if (is_null($vat)) {
                $vat = floatval(\oxRegistry::getConfig()->getConfigParam('dDefaultVAT'));
            }

            $isNetPrices = \oxRegistry::getConfig()->getShopConfVar('blEnterNetPrice');

            $group = $data->getCustomerGroupId()->getEndpoint();

            $sPrices = array();

            foreach ($data->getItems() as $item) {
                if (!$isNetPrices) {
                    $item->setNetPrice(floatval(($item->getNetprice() / 100) * (100 + $vat)));
                }

                if ($group === 'oxidcustomer' || empty($group)) {
                    if ($item->getQuantity() === 0) {
                        $this->db->execute('UPDATE oxarticles SET OXPRICE='.$item->getNetPrice().' WHERE OXID="'.$id.'"');
                        $mainPrice = $item->getNetPrice();
                    } else {
                        $sPrices[] = array(
                            'quantity' => $item->getQuantity(),
                            'netPrice' => $item->getNetPrice()
                        );
                    }
                }
                elseif (in_array(strtoupper(substr($group, -1)), static::$groups)) {
                    $this->db->execute('UPDATE oxarticles SET OXPRICE'.strtoupper(substr($group, -1)).'='.$item->getNetPrice().' WHERE OXID="'.$id.'"');
                }
            }

            usort($sPrices, function($a, $b) {
                if ($a['quantity'] == $b['quantity']) {
                    return 0;
                }
                return ($a['quantity'] < $b['quantity']) ? -1 : 1;
            });

            if ($mainPrice) {
                for ($i = 0; $i < count($sPrices); $i++) {
                    $max = $i < count($sPrices) - 1 ? $sPrices[$i + 1]['quantity'] : 99999;
                    $sPrice = new \stdClass();
                    $sPrice->OXID = $this->utils->oxid();
                    $sPrice->OXSHOPID = 'oxbaseshop';
                    $sPrice->OXARTID = $id;
                    //$sPrice->OXADDABS = $sPrices[$i]['netPrice'];
                    $sPrice->OXADDABS = 0;
                    $sPrice->OXADDPERC = 100 - (100 / $mainPrice * $sPrices[$i]['netPrice']);
                    $sPrice->OXAMOUNT = $sPrices[$i]['quantity'];
                    $sPrice->OXAMOUNTTO = $max;

                    $this->db->insert($sPrice, 'oxprice2article');
                }
            }
        }
    }
}
