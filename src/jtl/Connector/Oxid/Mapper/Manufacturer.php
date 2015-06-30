<?php
namespace jtl\Connector\Oxid\Mapper;

use \jtl\Connector\Oxid\Mapper\BaseMapper;

class Manufacturer extends BaseMapper
{
	protected $endpointModel = '\oxManufacturer';

	protected $pull = array(
		'id' => 'OXID',
		'name' => 'OXTITLE',
		'i18ns' => 'ManufacturerI18n'
	);

	protected $push = array(
		'OXID' => 'id',
		'OXSHOPID' => null,
		'ManufacturerI18n' => 'i18ns'		
	);

	protected function oxshopid($data)
	{
		return 'oxbaseshop';
	}
}
