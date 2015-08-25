<?php
namespace jtl\Connector\Oxid\Controller;

use jtl\Connector\Model\Identity;
use jtl\Connector\Oxid\Controller\ProductPrice;

class Product extends BaseController
{	
	private static $cache = array();

    public function pullData($data, $model, $limit = null)
	{
		$limit = 25;

        $result = $this->db->getAll('
			SELECT d.*,a.*,(SELECT COUNT(v.OXID) FROM oxarticles v WHERE v.OXPARENTID = a.OXID) AS combis
			FROM oxarticles a
			LEFT JOIN oxartextends d ON d.OXID = a.OXID
			LEFT JOIN jtl_connector_link l ON a.OXID = l.endpointId AND l.type = 64
            WHERE l.hostId IS NULL 
            LIMIT '.$limit
        );

		$return = array();

		foreach ($result as $data) {
			$model = $this->mapper->toHost($data);

			$return[] = $model;
		}

		return $return;
	}

	public function pushData($data)
	{
        $product = $this->mapper->toEndpoint($data);

        $existingId = $data->getId()->getEndpoint();

        if ($data->getIsMasterProduct() === true && !empty($existingId)) {
            $vars = $this->db->getAll('SELECT COUNT(OXID) AS varCount, SUM(OXSTOCK) as totalStock FROM oxarticles WHERE OXPARENTID="'.$existingId.'"');

            if (count($vars) > 0) {
                $product->assign(array(
                    'oxvarstock' => $vars[0]['totalStock'],
                    'oxvarcount' => $vars[0]['varCount']
                ));
            }
        }

        $id = $product->save();
		
		$data->getId()->setEndpoint($id);

        $cats = new Product2Category();
        $cats->pushData($data);

        $attrs = new ProductAttr();
        $attrs->pushData($data);

		return $data;
	}

	public function prePush($data)
	{
		$id = $data->getId()->getEndpoint();

		if (!empty($id)) {
			$this->db->execute('DELETE FROM oxobject2attribute WHERE OXOBJECTID="'.$id.'"');
			$this->db->execute('DELETE FROM oxobject2category WHERE OXOBJECTID="'.$id.'"');
			$this->db->execute('DELETE FROM oxobject2seodata WHERE OXOBJECTID="'.$id.'"');
			$this->db->execute('DELETE FROM oxseo WHERE OXOBJECTID="'.$id.'"');
            $this->db->execute('DELETE FROM oxprice2article WHERE OXARTID="'.$id.'"');
            $this->db->execute('UPDATE oxarticles SET OXPRICEA=0, OXPRICEB=0,OXPRICEC=0 WHERE OXID="'.$id.'"');
		} else {
            $data->getId()->setEndpoint($this->utils->oxid());
        }

        $parentHost = $data->getMasterProductId()->getHost();

        if (!empty($parentHost)) {
            $parentEndpoint = $data->getMasterProductId()->getEndpoint();

            if (empty($parentEndpoint)) {
                if (isset(static::$cache[$parentHost])) {
                    $data->getMasterProductId()->setEndpoint(static::$cache[$data->getMasterProductId()->getHost()]);
                } else {
                    throw new \Exception('Child pushed with unknown parent');
                }
            }
        }
	}

    public function postPush($data, $model)
    {
        $id = $data->getId()->getEndpoint();
        $parent = $data->getMasterProductId()->getEndpoint();

        if (!empty($id)) {
            foreach ($data->getPrices() as $priceObj) {
                $priceObj->setProductId(new Identity($id));
            }

            $price = new ProductPrice();
            $price->pushData($data);
        }

        if (!empty($parent)) {
            $vars = $this->db->getAll('SELECT COUNT(OXID) AS varCount, SUM(OXSTOCK) as totalStock FROM oxarticles WHERE OXPARENTID="'.$parent.'"');

            if (count($vars) > 0) {
                $this->db->execute('UPDATE oxarticles SET OXVARSTOCK='.$vars[0]['totalStock'].', OXVARCOUNT='.$vars[0]['varCount'].' WHERE OXID="'.$parent.'"');
            }
        }

        static::$cache[$data->getId()->getHost()] = $data->getId()->getEndpoint();
    }

    public function finishPush()
    {
        array_map('unlink', glob(\oxRegistry::getConfig()->getConfigParam("sCompileDir").'*'));
    }

	public function deleteData($data)
	{
		$product = new \oxArticle();

		if (!$product->delete($data->getId()->getEndpoint())) {
			//throw new \Exception('Error deleting product with id: '.$data->getId()->getEndpoint());
		} else {
            $this->postPush($data);
        }

		return $data;
	}

	public function getStats()
	{
		return $this->db->GetOne('
			SELECT COUNT(*) 
			FROM oxarticles a 
			LEFT JOIN jtl_connector_link l ON a.OXID = l.endpointId AND l.type = 64
            WHERE l.hostId IS NULL
        ');
	}
}
