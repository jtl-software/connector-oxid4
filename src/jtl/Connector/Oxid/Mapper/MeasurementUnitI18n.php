<?php
namespace jtl\Connector\Oxid\Mapper;

class MeasurementUnitI18n extends BaseMapper
{
	protected $pull = array(
		'languageISO' => 'iso',
		'measurementUnitId' => 'unitId',
		'name' => 'name'
	);
}
