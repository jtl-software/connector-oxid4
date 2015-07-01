<?php
namespace jtl\Connector\Oxid\Mapper;

class MeasurementUnit extends BaseMapper
{
	protected $pull = array(
		'id' => 'id',
		'code' => null,
		'displayCode' => null,
		'i18ns' => 'MeasurementUnitI18n'
	);

	protected function code($data)
	{
		return substr(strrchr($data['id'], "_"), 1);
	}

	protected function displayCode($data)
	{
		return substr(strrchr($data['id'], "_"), 1);
	}
}
