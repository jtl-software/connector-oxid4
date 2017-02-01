<?php
namespace jtl\Connector\Oxid\Controller;

use jtl\Connector\Application\Application;
use \jtl\Connector\Drawing\ImageRelationType;
use \jtl\Connector\Model\Identity;
use \jtl\Connector\Model\Image as ImageModel;

class Image extends BaseController {
    private $cols = 7;
    private $imgUrl;
    private $imgPath;
    protected $mapper;

    public function __construct()
    {
        parent::__construct();

        $oxConfig = \oxRegistry::getConfig();
        $this->imgUrl = $oxConfig->getPictureUrl();
        $this->imgPath = $oxConfig->getPictureDir();
        $this->mapper = Application::getInstance()->getConnector()->getPrimaryKeyMapper();
    }

    public function pullData($data, $model, $limit = null)
    {
        $return = array();

        $result = array_merge(
            $this->getProductImgs(),
            $this->getCategoryImgs(),
            $this->getManufacturerImgs()
        );

        if (count($result) > 0) {
            foreach ($result as $imgData) {
                $img = new ImageModel();
                $img->setRelationType($imgData['type']);
                $img->setForeignKey(new Identity($imgData['OXID']));
                $img->setId(new Identity($imgData['id']));
                $img->setFilename($imgData['pic']);
                $img->setRemoteUrl($imgData['path']);
                $img->setSort($imgData['sort']);

                $return[] = $img;

                if (count($return) >= $limit) {
                    return $return;
                }
            }
        }

        return $return;
    }

    public function pushData($data)
    {
        if (get_class($data) === 'jtl\Connector\Model\Image') {
            $foreignId = $data->getForeignKey()->getEndpoint();

            if (!is_null($foreignId)) {
                $this->deleteData($data);
            }

            $imgFileName = substr($data->getFilename(), strrpos($data->getFilename(), '/') + 1);

            switch ($data->getRelationType()) {
                case ImageRelationType::TYPE_CATEGORY:
                    if (!rename($data->getFilename(), $this->imgPath.'master/category/thumb/'.$imgFileName)) {
                        throw new \Exception('Cannot move uploaded image file');
                    }

                    $this->db->execute('UPDATE oxcategories SET OXTHUMB = "'.$imgFileName.'" WHERE OXID = "'.$foreignId.'"');

                    $endpointId = 'c'.'_'.$foreignId;
                    break;
                case ImageRelationType::TYPE_MANUFACTURER:
                    if (!rename($data->getFilename(), $this->imgPath.'master/manufacturer/icon/'.$imgFileName)) {
                        throw new \Exception('Cannot move uploaded image file');
                    }

                    $this->db->execute('UPDATE oxmanufacturers SET OXICON = "'.$imgFileName.'" WHERE OXID = "'.$foreignId.'"');

                    $endpointId = 'm'.'_'.$foreignId;
                    break;
                case ImageRelationType::TYPE_PRODUCT:
                    if (!rename($data->getFilename(), $this->imgPath.'master/product/'.$data->getSort().'/'.$imgFileName)) {
                        throw new \Exception('Cannot move uploaded image file');
                    }

                    $this->db->execute('UPDATE oxarticles SET OXPIC'.$data->getSort().' = "'.$imgFileName.'" WHERE OXID = "'.$foreignId.'"');

                    $endpointId = 'p'.$data->getSort().'_'.$foreignId;
                    break;
            }

            $this->mapper->save($endpointId, $data->getId()->getHost(), 16);

            $data->getId()->setEndpoint($endpointId);
        } else {
            throw new \Exception('Data is not an valid image model.');
        }

        return $data;
    }

    public function deleteData($data)
    {
        $foreignId = $data->getForeignKey()->getEndpoint();
        $id = $data->getId()->getEndpoint();

        if (!is_null($id) && !is_null($foreignId)) {
            switch ($data->getRelationType()) {
                case ImageRelationType::TYPE_CATEGORY:
                    $cat = new \oxCategory();
                    $cat->load($foreignId);

                    $utilsFile = \oxRegistry::get("oxUtilsFile");
                    $utilsPic = \oxRegistry::get("oxUtilsPic");

                    $utilsPic->safePictureDelete($cat->oxcategories__oxthumb->value, $this->imgPath . $utilsFile->getImageDirByType('TC'), 'oxcategories', 'oxthumb');

                    $cat->oxcategories__oxthumb = new \oxField();
                    $cat->save();
                    break;
                case ImageRelationType::TYPE_MANUFACTURER:
                    $manufacturer = new \oxManufacturer();
                    $manufacturer->load($foreignId);

                    $utilsFile = \oxRegistry::get("oxUtilsFile");
                    $utilsPic = \oxRegistry::get("oxUtilsPic");

                    $utilsPic->safePictureDelete($manufacturer->oxmanufacturers__oxicon->value, $this->imgPath . $utilsFile->getImageDirByType('MICO'), 'oxmanufacturers', 'oxicon');

                    $manufacturer->oxmanufacturers__oxicon = new \oxField();
                    $manufacturer->save();
                    break;
                case ImageRelationType::TYPE_PRODUCT:
                    preg_match('~p(\d)_~', $id, $col);

                    $article = new \oxArticle();
                    $article->load($foreignId);

                    if ($article->{"oxarticles__oxpic".$col[1]}->value) {
                        if (!$article->isDerived()) {
                            $handler = \oxRegistry::get("oxPictureHandler");
                            $handler->deleteArticleMasterPicture($article, $col[1]);
                        }

                        $article->{"oxarticles__oxpic".$col[1]} = new \oxField();

                        if (isset($article->{"oxarticles__oxzoom" . $col[1]})) {
                            $article->{"oxarticles__oxzoom".$col[1]} = new \oxField();
                        }

                        $article->save();
                    }

                    break;
            }

            $this->db->execute('DELETE FROM jtl_connector_link WHERE hostId = '.$data->getId()->getHost().' && type = 16');
        }

        return $data;
    }

    public function getStats()
    {
        $count = 0;

        $count += count($this->getProductImgs());
        $count += count($this->getCategoryImgs());
        $count += count($this->getmanufacturerImgs());

        return $count;
    }

    private function getProductImgs()
    {
        $return = array();

        for ($i = 1; $i < $this->cols; $i++) {
            $result = $this->db->getAll('
              SELECT p.* FROM (
                SELECT a.OXID, a.OXPIC'.$i.' AS pic, CONCAT("p'.$i.'_",a.OXID) AS id
                FROM oxarticles a
                WHERE a.OXPIC'.$i.' IS NOT NULL && a.OXPIC'.$i.' != ""
              ) AS p
              LEFT JOIN jtl_connector_link l ON p.id = l.endpointId AND l.type = 16
              WHERE l.hostId IS NULL');

            if (count($result) > 0) {
                foreach ($result as $imgData) {
                    $imgData['type'] = 'product';
                    $imgData['sort'] = $i;
                    $imgData['path'] = $this->imgUrl.'master/product/'.$i.'/'.$imgData['pic'];

                    $return[] = $imgData;
                }
            }
        }

        return $return;
    }

    private function getCategoryImgs()
    {
        $return = array();

        $result = $this->db->getAll('
            SELECT c.OXID, c.OXTHUMB AS pic, CONCAT("c_",c.OXID) AS id
            FROM oxcategories c
            LEFT JOIN jtl_connector_link l ON CONCAT("c_",c.OXID) = l.endpointId AND l.type = 16
            WHERE l.hostId IS NULL && c.OXTHUMB IS NOT NULL && c.OXTHUMB != ""
        ');

        if (count($result) > 0) {
            foreach ($result as $imgData) {
                $imgData['type'] = 'category';
                $imgData['sort'] = 1;
                $imgData['path'] = $this->imgUrl.'master/category/thumb/'.$imgData['pic'];

                $return[] = $imgData;
            }
        }

        return $return;
    }

    private function getManufacturerImgs()
    {
        $return = array();

        $result = $this->db->getAll('
            SELECT m.OXID, m.OXICON AS pic, CONCAT("m_",m.OXID) AS id
            FROM oxmanufacturers m
            LEFT JOIN jtl_connector_link l ON CONCAT("m_",m.OXID) = l.endpointId AND l.type = 16
            WHERE l.hostId IS NULL && m.OXICON IS NOT NULL && m.OXICON != ""
        ');

        if (count($result) > 0) {
            foreach ($result as $imgData) {
                $imgData['type'] = 'manufacturer';
                $imgData['sort'] = 1;
                $imgData['path'] = $this->imgUrl.'master/manufacturer/icon/'.$imgData['pic'];

                $return[] = $imgData;
            }
        }

        return $return;
    }
}
