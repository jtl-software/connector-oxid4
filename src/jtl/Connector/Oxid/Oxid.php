<?php
namespace jtl\Connector\Oxid;

use \jtl\Connector\Core\Rpc\RequestPacket;
use \jtl\Connector\Core\Utilities\RpcMethod;
use \jtl\Connector\Core\Rpc\ResponsePacket;
use \jtl\Connector\Session\SessionHelper;
use \jtl\Connector\Base\Connector as BaseConnector;
use \jtl\Connector\Core\Rpc\Error as Error;
use \jtl\Connector\Core\Http\Response;
use \jtl\Connector\Oxid\Mapper\PrimaryKeyMapper;
use \jtl\Connector\Result\Action;
use \jtl\Connector\Oxid\Auth\TokenLoader;
use \jtl\Connector\Oxid\Checksum\ChecksumLoader;
use \jtl\Connector\Core\Logger\Logger;

class Oxid extends BaseConnector
{
    protected $controller;
    protected $action;

    public function initialize()
    {
        $this->setPrimaryKeyMapper(new PrimaryKeyMapper());
        $this->setTokenLoader(new TokenLoader());
        $this->setChecksumLoader(new ChecksumLoader());
    }

    public function canHandle()
    {
        $controller = RpcMethod::buildController($this->getMethod()->getController());
        $class = "\\jtl\\Connector\\Oxid\\Controller\\{$controller}";

        if (class_exists($class)) {
            $this->controller = $class::getInstance();
            $this->action = RpcMethod::buildAction($this->getMethod()->getAction());

            return is_callable(array($this->controller, $this->action));
        }

        return false;        
    }

    public function handle(RequestPacket $requestpacket)
    {
        $this->controller->setMethod($this->getMethod());

        $actionExceptions = array(
            'pull',
            'statistic',
            'identify'
        );

        $callExceptions = array(
            //'image.push'
        );

        if (!in_array($this->action, $actionExceptions) && !in_array($requestpacket->getMethod(), $callExceptions)) {
            if (!is_array($requestpacket->getParams())) {
                throw new \Exception('data is not an array');
            }

            $action = new Action();
            $results = array();

            if (method_exists($this->controller, 'initPush')) {
                $this->controller->initPush($requestpacket->getParams());
            }

            foreach ($requestpacket->getParams() as $param) {
                $result = $this->controller->{$this->action}($param);
                $results[] = $result->getResult();
            }

            if (method_exists($this->controller, 'finishPush')) {
                $this->controller->finishPush($requestpacket->getParams(), $results);
            }

            $action->setHandled(true)
                ->setResult($results)
                ->setError($result->getError());

            return $action;
        }

        return $this->controller->{$this->action}($requestpacket->getParams());
    }
}
