<?php
namespace jtl\Connector\Oxid\Controller;

class GlobalData extends BaseController
{	
	public function pullData($data, $model, $limit = null)
	{
		$model = $this->mapper->toHost();

		return [$model];
	}

	public function pushData($data)
	{
		$customerGroupController = new CustomerGroup();
        $customerGroupController->pushData($data);

        return $data;
	}
}
