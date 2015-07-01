<?php
namespace jtl\Connector\Oxid\Mapper;

class Language extends BaseMapper
{
	protected $pull = array(
		'id' => 'column',
		'isDefault' => null,
		'languageISO' => 'iso3',
		'nameEnglish' => 'name',
		'nameGerman' => 'name'
	);

	protected function isDefault($data)
	{
		return $data['column'] === 0 ? true : false;
	}
}
