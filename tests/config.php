<?php

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
);

spl_autoload_register('Moneybird\ApiConnector::autoload');

if (file_exists(dirname(__FILE__).'/my-config.php')) {
	include(dirname(__FILE__).'/my-config.php');
}

if (!function_exists('getTransport')) {
	function getTransport($config) {
		if ($config['authType'] == 'oauth') {
			$transport = new Moneybird\HttpClient_Oauth();
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