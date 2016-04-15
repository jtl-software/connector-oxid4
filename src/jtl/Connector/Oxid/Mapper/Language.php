<?php
namespace jtl\Connector\Oxid\Mapper;

class Language extends BaseMapper
{
	protected $pull = array(
		'id' => 'column',
		'isDefault' => 'default',
		'languageISO' => 'iso3',
		'nameEnglish' => 'name',
		'nameGerman' => 'name'
	);
}
