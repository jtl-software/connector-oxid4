<?php
namespace jtl\Connector\Oxid\Controller;

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
            //'Customer',
            //'CustomerOrder',
            //'Image',
            //'Product',
            'Manufacturer',
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

        $connector = new ConnectorIdentification();
        $connector->setEndpointVersion($aModule['version']);
        $connector->setPlatformName('OXID eshop');
        $connector->setPlatformVersion(\oxRegistry::getConfig()->getVersion());
        $connector->setProtocolVersion(Application()->getProtocolVersion());

        $action->setResult($connector);

        return $action;
    }
}
