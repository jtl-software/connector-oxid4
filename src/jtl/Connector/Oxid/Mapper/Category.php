<?php
namespace jtl\Connector\Oxid\Mapper;

class Category extends BaseMapper
{
	protected $endpointModel = '\oxCategory';

	protected $pull = array(
		'id' => 'OXID',
		'parentCategoryId' => 'OXPARENTID',
		'isActive' => 'OXACTIVE',
		'sort' => 'OXSORT',
		'i18ns' => 'CategoryI18n',
		'attributes' => 'CategoryAttr'
	);

	protected $push = array(
		'OXID' => 'id',
		'OXPARENTID' => null,
		'OXACTIVE' => 'isActive',
		'OXSORT' => 'sort',
		'OXSHOPID' => null,
		'CategoryI18n' => 'i18ns',
		'CategoryAttr' => 'attributes'
	);

	protected function oxshopid($data)
	{
		return 'oxbaseshop';
	}

	protected function oxparentid($data)
	{
		$parent = $data->getParentCategoryId()->getEndpoint();

		return empty($parent) ? 'oxrootid' : $parent;
	}
}
