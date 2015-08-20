<?php
$sMetadataVersion = '1.1';

$aModule = array(
    'id' => 'jtl-connector',
    'title' => 'JTL Connector',
    'description' => 'JTL Connector',
    'version' => '1.0.2',
    'author' => 'JTL Software GmbH',
    'url' => 'http://jtl-software.de',
    'email' => 'daniel.hoffmann@jtl-software.com',
    'thumbnail' => 'application/views/logo.png',
    'extend' => array(
        'oxsession' => 'jtl-connector/application/extend/Session'
    ),
	'files' => array(
        'JTLConnector' => 'jtl-connector/jtlconnector.php',
        'JTLConnectorAdmin' => 'jtl-connector/jtlconnectoradmin.php'
    ),
    'events' => array(
        'onActivate'   => 'JTLConnector::onActivate',
        'onDeactivate' => 'JTLConnector::onDeactivate'
    ),
    'settings' => array(
        array(
            'group' => 'connector', 
            'name' => 'password', 
            'type' => 'str'
        )     
    ),
    'templates' => array(
        'jtlconnector.tpl' => 'jtl-connector/application/views/admin/tpl/jtlconnector.tpl'        
    )    
);
