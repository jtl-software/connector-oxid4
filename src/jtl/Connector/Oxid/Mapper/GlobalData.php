<?php
namespace jtl\Connector\Oxid\Mapper;

use \jtl\Connector\Oxid\Mapper\BaseMapper;

class GlobalData extends BaseMapper
{
	protected $pull = array(
		'languages' => 'Language',
		'currencies' => 'Currency',
		'measurementUnits' => 'MeasurementUnit',
        'taxRates' => 'TaxRate',
        'customerGroups' => 'CustomerGroup'
	);
}
