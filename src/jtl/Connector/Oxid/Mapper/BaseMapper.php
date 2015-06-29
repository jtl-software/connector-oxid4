<?php
namespace jtl\Connector\Oxid\Mapper;

use \jtl\Connector\Model\Identity;

class BaseMapper
{
	protected $db = null;
	protected $utils = null;
	private $model = null;
	private $type;
	protected $endpointModel = null;

	public function __construct()
	{
		$reflect = new \ReflectionClass($this);
		$typeClass = "\\jtl\\Connector\\Type\\{$reflect->getShortName()}";

		$this->db = \oxDb::getInstance()->getDb(\oxDb::FETCH_MODE_ASSOC);
		$this->utils = \jtl\Connector\Oxid\Utils\Utils::getInstance();
        $this->model = "\\jtl\\Connector\\Model\\{$reflect->getShortName()}";   
        $this->type = new $typeClass();        
	}

	public function toHost($data)
	{
		$model = new $this->model();

		foreach ($this->pull as $host => $endpoint) {
			$setter = 'set'.ucfirst($host);

			$value = $data[$endpoint];		
			$property = $this->type->getProperty($host);

			if ($property->isNavigation()) {
				$subControllerName = "\\jtl\\Connector\\Oxid\\Controller\\".$endpoint;
				
				if (class_exists($subControllerName)) {
					$subController = new $subControllerName();
					$value = $subController->pullData($data, $model);
				}
			} elseif ($property->isIdentity()) {
				$value = new Identity($value);
			} elseif ($property->getType() == 'boolean') {
				$value = (bool) $value;
			} elseif ($property->getType() == 'integer') {
				$value = intval($value);
			}
		

			$model->$setter($value);
		}

		return $model;
	}

	public function toEndpoint($data)
	{
		$model = new $this->endpointModel();

		$assign = array();

		foreach ($this->push as $endpoint => $host) {
			$getter = 'get'.ucfirst($host);

			$value = $data->$getter();
			$property = $this->type->getProperty($host);

			if ($property->isNavigation()) {
				$subControllerName = "\\jtl\\Connector\\Oxid\\Controller\\".$endpoint;
				
				if (class_exists($subControllerName)) {
					$subController = new $subControllerName();
					$subController->pushData($data, $model);
				}
			} elseif ($property->isIdentity()) {
				$value = $value->getEndpoint();
			}			

			$assign[$endpoint] = $value;			
		}

		$model->assign($assign);

		return $model;
	}
}
