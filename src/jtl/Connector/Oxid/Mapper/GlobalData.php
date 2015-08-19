<?php
namespace jtl\Connector\Oxid\Mapper;

class GlobalData extends BaseMapper
{
	protected $pull = array(
		'languages' => 'Language',
		'currencies' => 'Currency',
		'measurementUnits' => 'MeasurementUnit',
        'taxRates' => 'TaxRate',
        'customerGroups' => 'CustomerGroup',
		'shippingMethods' => 'ShippingMethod'
	);
}
