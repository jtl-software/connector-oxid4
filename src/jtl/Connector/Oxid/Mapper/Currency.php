<?php
namespace jtl\Connector\Oxid\Mapper;

class Currency extends BaseMapper
{
	protected $pull = array(
		'id' => 0,
		'factor' => 1,
		'delimiterCent' => 2,
		'delimiterThousand' => 3,
		'nameHtml' => 4,
		'name' => 0,
		'iso' => 0
	);
}
