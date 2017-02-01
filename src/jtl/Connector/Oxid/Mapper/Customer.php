<?php
namespace jtl\Connector\Oxid\Mapper;

use \jtl\Connector\Oxid\Mapper\BaseMapper;

class Customer extends BaseMapper
{
	protected $endpointModel = '\oxUser';

	protected $pull = array(
		'id' => 'OXID',
		'isActive' => 'OXACTIVE',
		'eMail' => 'OXUSERNAME',
		'customerNumber' => 'OXCUSTNR',
		'vatNumber' => 'OXUSTID',
		'company' => 'OXCOMPANY',
		'firstName' => 'OXFNAME',
		'lastName' => 'OXLNAME',
		'street' => null,
		'zipCode' => 'OXZIP',
		'city' => 'OXCITY',
		'phone' => 'OXFON',
		'fax' => 'OXFAX',
		'mobile' => 'OXMOBFON',
		'salutation' => null,
		'creationDate' => 'OXCREATE',
		'countryIso' => 'OXISOALPHA3',
		'birthday' => 'OXBIRTHDATE',
		'languageISO' => 'OXISOALPHA3',
		'state' => 'OXTITLE'
	); 

	protected $push = array(
		'OXID' => 'id',
		'OXACTIVE' => 'isActive',
		'OXUSERNAME' => 'eMail',
		'OXCUSTNR' => 'customerNumber',
		'OXUSTID' => 'vatNumber',
		'OXCOMPANY' => 'company',
		'OXFNAME' => 'firstName',
		'OXLNAME' => 'lastName',
		'OXSTREET' => 'street',
		'OXZIP' => 'zipCode',
		'OXCITY' => 'city',
		'OXFON' => 'phone',
		'OXFAX' => 'fax',
		'OXMOBFON' => 'mobile',
		'OXSAL' => null,
		'OXCREATE' => 'creationDate',
		'OXCOUNTRYID' => null,
		'OXBIRTHDATE' => 'birthday',
		'OXSTATEID' => null,
		'OXSHOPID' => null
	);

	protected function street($data)
	{
		return $data['OXSTREET'].' '.$data['OXSTREETNR'];
	}

	protected function salutation($data)
	{
		return $data['OXSAL'] == 'MR' ? 'm' : 'w';
	}

	protected function oxshopid($data)
	{
		return 'oxbaseshop';
	}

	protected function oxcountryid($data)
	{
		return $this->db->GetOne('SELECT OXID FROM oxcountry WHERE OXISOALPHA3="'.$data->getLanguageISO().'"');
	}

	protected function oxstateid($data)
	{
		return $this->db->GetOne('SELECT OXID FROM oxstates WHERE OXTITLE="'.$data->getState().'"');
	}

	protected function oxsal($data)
	{
		return $data->getSalutation() === 'm' ? 'MR' : 'MRS';
	}
}
