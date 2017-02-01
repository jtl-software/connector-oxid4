<?php
namespace jtl\Connector\Oxid\Controller;

use jtl\Connector\Model\ConnectorServerInfo;
use \jtl\Connector\Result\Action;
use \jtl\Connector\Model\Statistic;
use \jtl\Connector\Core\Controller\Controller;
use \jtl\Connector\Core\Model\DataModel;
use \jtl\Connector\Core\Model\QueryFilter;
use \jtl\Connector\Model\ConnectorIdentification;
use \jtl\Connector\Core\Rpc\Error;

class Connector extends Controller
{
    public function statistic(QueryFilter $filter)
    {
        $action = new Action();
        $action->setHandled(true);

        $return = [];

        $mainControllers = array(
            'Category',
            'Customer',
            'CustomerOrder',
            'Image',
            'Product',
            'Manufacturer',
            'Payment'
        );

        foreach ($mainControllers as $controller) {
            $class = "\\jtl\\Connector\\Oxid\\Controller\\{$controller}";

            if (class_exists($class)) {
                try {
                    $controllerObj = new $class();
                    
                    $statModel = new Statistic();

                    $statModel->setAvailable(intval($controllerObj->getStats()));
                    $statModel->setControllerName(lcfirst($controller));
                    
                    $return[] = $statModel;
                } catch (\Exception $exc) {
                    $err = new Error();
                    $err->setCode($exc->getCode());
                    $err->setMessage($exc->getMessage());
                    $action->setError($err);
                }
            }
        }

        $action->setResult($return);

        return $action;
    }

    public function identify()
    {
        $action = new Action();
        $action->setHandled(true);

        include(CONNECTOR_DIR.'/metadata.php');

        $returnMegaBytes = function($value) {
            $value = trim($value);
            $unit = strtolower($value[strlen($value) - 1]);
            switch ($unit) {
                case 'g':
                    $value *= 1024;
            }

            return (int) $value;
        };

        $serverInfo = new ConnectorServerInfo();
        $serverInfo->setMemoryLimit($returnMegaBytes(ini_get('memory_limit')))
            ->setExecutionTime((int) ini_get('max_execution_time'))
            ->setPostMaxSize($returnMegaBytes(ini_get('post_max_size')))
            ->setUploadMaxFilesize($returnMegaBytes(ini_get('upload_max_filesize')));

        $connector = new ConnectorIdentification();
        $connector->setEndpointVersion($aModule['version']);
        $connector->setPlatformName('OXID eShop');
        $connector->setPlatformVersion(\oxRegistry::getConfig()->getVersion());
        $connector->setProtocolVersion(Application()->getProtocolVersion());
        $connector->setServerInfo($serverInfo);

        $action->setResult($connector);

        return $action;
    }
}
