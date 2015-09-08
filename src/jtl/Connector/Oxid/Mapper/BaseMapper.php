<?php
namespace jtl\Connector\Oxid\Mapper;

use \jtl\Connector\Model\Identity;
use \jtl\Connector\Oxid\Utils\Db;

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

		$this->db = DB::getInstance();
		$this->utils = \jtl\Connector\Oxid\Utils\Utils::getInstance();
        $this->model = "\\jtl\\Connector\\Model\\{$reflect->getShortName()}";   
        $this->type = new $typeClass();        
	}

	public function toHost($data)
	{
		$model = new $this->model();

		foreach ($this->pull as $host => $endpoint) {
			$setter = 'set'.ucfirst($host);			
			$fnName = strtolower($host);

			if (method_exists($this, $fnName)) {
				$value = $this->$fnName($data);
			} else {
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
				} elseif ($property->getType() == 'double') {
					$value = floatval($value);
				} elseif ($property->getType() == 'DateTime') {
					$value = $value == '0000-00-00' || $value == '0000-00-00 00:00:00' ? null : new \DateTime($value);
				}
			}		

			if (!empty($value)) $model->$setter($value);
		}

		return $model;
	}

	public function toEndpoint($data, $customData = null)
	{
        $model = new $this->endpointModel();

		if (isset($this->id)) {
            $idGetter = 'get'.ucfirst($this->id);

            if (method_exists($data, $idGetter)) {
                $existingId = $data->$idGetter()->getEndpoint();

                if ($existingId) {
                    $model->load($existingId);
                }
            }
        }

		$assign = array();

		foreach ($this->push as $endpoint => $host) {
			$fnName = strtolower($endpoint);

			if (method_exists($this, $fnName)) {
				$value = $this->$fnName($data, $customData);
			} else {
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
				} elseif ($property->getType() == 'DateTime') {
					$value = $value === null ? '0000-00-00 00:00:00' : $value->format('Y-m-d H:i:s');
				}
			}
			
			$assign[$endpoint] = $value;			
		}

		$model->assign($assign);

		return $model;
	}
}
