<?php

/*
 * HttpClient_Oauth class for making requests over http with oauth
 */

namespace Moneybird;

include_once (__DIR__.'/../Lib/Oauth.php');

/**
 * Wrapper for curl to create http requests
 */
class HttpClient_Oauth extends HttpClient {
	
	protected $consumer;
	protected $token;
	
	/**
	 * Not needed using Oauth
	 *
	 * @param string $username Username
	 * @param string $password Password
	 * @access public
	 * @return HttpClient
	 */
	public function setAuth($username, $password) {
		return $this;
	}
	
	/**
	 * Set Consumer and Token
	 *
	 * @param Lib\OAuthConsumer $consumer
	 * @param Lib\OAuthConsumer $token
	 * @return HttpClient_Oauth 
	 */
	public function setConsumerAndToken(Lib\OAuthConsumer $consumer, Lib\OAuthConsumer $token) {
		$this->consumer = $consumer;
		$this->token = $token;
		return $this;
	}
	
	/**
	 * Perform the request
	 * 
	 * @param string $url URL of request
	 * @param string $requestMethod (GET|POST|PUT|DELETE)
	 * @param string $data Data in string format
	 * @param array $headers
	 * @return string 
	 * @throws HttpClient_Exception
	 * @throws HttpClient_HttpStatusException
	 * @throws HttpClient_UnknownHttpStatusException
	 * @throws HttpClient_ConnectionErrorException
	 * @access public
	 */
	public function send($url, $requestMethod, $data = null, Array $headers = null) {
		$params = array();
		if (false !== ($pos = strpos($url, '?'))) {
			$paramPairs = explode('&', substr($url, $pos + 1));
			foreach ($paramPairs as $pair) {
				$pairSplit = explode('=', $pair);
				$params[$pairSplit[0]] = isset($pairSplit[1]) ? $pairSplit[1] : null;
			}
		}
		$request = Lib\OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $requestMethod, $url, $params);
		$request->sign_request(new Lib\OAuthSignatureMethod_HMAC_SHA1(), $this->consumer, $this->token);
	
		if (is_null($headers)) {
			$headers = array();
		}
		$headers = array_merge($headers, array($request->to_header()));
		
		return parent::send($url, $requestMethod, $data, $headers);
	}
	
}