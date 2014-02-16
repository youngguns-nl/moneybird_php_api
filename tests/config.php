<?php

error_reporting(E_ALL);

$config = array(
	'clientname' => '',
	'username'   => '',
	'password'   => '',
	'authType'   => 'http',
	'oauth'      => array(
		'consumerKey' => '',
		'consumerSecret' => '',
		'token' => '',
		'tokenSecret' => '',
	),
	'testcontact' => '',
);

require_once dirname(__FILE__) . '/../ApiConnector.php';

spl_autoload_register('Moneybird\ApiConnector::autoload');

Moneybird\ApiConnector::$debug = true;

if (file_exists(dirname(__FILE__).'/my-config.php')) {
	include(dirname(__FILE__).'/my-config.php');
}

if (!function_exists('getTransport')) {
	function getTransport($config) {
		if ($config['authType'] == 'oauth') {
			$transport = new Moneybird\HttpClient\Oauth();
			$consumer = new Moneybird\Lib\OAuthConsumer($config['oauth']['consumerKey'], $config['oauth']['consumerSecret'], NULL);
			$token = new Moneybird\Lib\OAuthConsumer($config['oauth']['token'], $config['oauth']['tokenSecret']);
			$transport->setConsumerAndToken($consumer, $token);	
		} else {
			$transport = new Moneybird\HttpClient();
			$transport->setAuth(
				$config['username'], 
				$config['password']
			);
		}
		return $transport;	
	}
}