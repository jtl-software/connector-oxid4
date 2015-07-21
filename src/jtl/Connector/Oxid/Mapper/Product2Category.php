<?php
namespace jtl\Connector\Oxid\Mapper;

class Product2Category extends BaseMapper
{
	protected $pull = array(
		'id' => 'OXID',
		'categoryId' => 'OXCATNID',
		'productId' => 'OXOBJECTID'
	);
}
