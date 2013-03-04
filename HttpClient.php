<?php

/*
 * HttpClient class for making requests over http
 */

namespace Moneybird;

/**
 * Wrapper for curl to create http requests
 */
class HttpClient implements Transport {
	
	/**
	 * Curl handler
	 *
	 * @access protected
	 * @var resource
	 */
	protected $connection;
	
	/**
	 * Connection defaults
	 *
	 * @access protected
	 * @var array
	 */
	protected $connectionOptions = array();
	
	/**
	 * Filehandler for temp file
	 * 
	 * @access protected
	 * @var resource
	 */
	protected $tmpFile;
	
	/**
	 * Response string
	 * @access protected
	 * @var string
	 */
	protected $response;
	
	/**
	 * Http Status code
	 * 
	 * @access protected
	 * @var int
	 */
	protected $httpStatus;
	
	/**
	 * Headers of last request
	 * 
	 * @access protected
	 * @var string
	 */
	protected $lastHeader = '';
	
	/**
	 * Verify host and peer
	 * 
	 * @access protected
	 * @var bool
	 * @static
	 */
	public static $verifyHostAndPeer = true;
	
	/**
	 * Creates connector object
	 *
	 * @access public
	 */
	public function __construct(array $connectionOptions = array()) {
		$this->connectionOptions = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYHOST => self::$verifyHostAndPeer ? 2 : 0,
			CURLOPT_SSL_VERIFYPEER => self::$verifyHostAndPeer,
			CURLOPT_HEADER => true,
		) +
		$connectionOptions;
	}
	
	/**
	 * Clean up
	 * @access public
	 */
	public function __destruct() {
		$this->closeConnection();
	}
	
	/**
	 * Set credentials for the connection
	 *
	 * @param string $username Username
	 * @param string $password Password
	 * @access public
	 * @return HttpClient
	 */
	public function setAuth($username, $password) {
		$this->connectionOptions[CURLOPT_USERPWD] = $username . ':' . $password;
		$this->connectionOptions[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
		return $this;
	}
	
	/**
	 * Set useragent
	 * @param string $userAgent
	 * @access public
	 * @return HttpClient
	 */
	public function setUserAgent($userAgent) {
		$this->connectionOptions[CURLOPT_USERAGENT] = $userAgent;
		return $this;
	}
	
	/**
	 * Init connection
	 *
	 * @access protected
	 * @return HttpClient
	 * @throws HttpClient_ConnectionErrorException
	 */
	protected function openConnection() {
		$this->response = null;
		$this->httpStatus = null;
		
		if (!$this->connection = curl_init()) {
			throw new HttpClient_ConnectionErrorException('Unable to open connection');
		} 
		
		$this->setConnectionOptions($this->connectionOptions);
		return $this;
	}
	
	/**
	 * Set connection options
	 * 
	 * @param array $options 
	 * @throws HttpClient_ConnectionErrorException
	 * @access protected
	 * @return HttpClient
	 */
	protected function setConnectionOptions(Array $options) {
		if (!is_resource($this->connection)) {
			throw new HttpClient_ConnectionErrorException('cURL connection has not been set up yet!');
		}
		if (!curl_setopt_array($this->connection, $options)) {
			throw new HttpClient_ConnectionErrorException('Unable to set cURL options' . PHP_EOL . curl_error($this->connection));
		}
		return $this;
	}
	
	/**
	 * Close connection
	 *
	 * @access protected
	 * @return HttpClient
	 */
	protected function closeConnection() {
		if (is_resource($this->tmpFile)) {
			fclose($this->tmpFile);
		}
		if (is_resource($this->connection)) {
			curl_close($this->connection);
		}
		return $this;
	}
	
	/**
	 * Prepare GET request
	 * @return HttpClient 
	 * @access protected
	 */
	protected function preGetRequest() {
		$this->setConnectionOptions(array(
			CURLOPT_HTTPGET => true,
		));
		return $this;
	}
	
	/**
	 * Prepare POST request
	 * @return HttpClient 
	 * @access protected
	 */
	protected function prePostRequest($data) {
		$this->setConnectionOptions(array(
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $data,
		));
		return $this;
	}
	
	/**
	 * Prepare PUT request
	 * @return HttpClient 
	 * @access protected
	 */
	protected function prePutRequest($data) {
		$this->tmpFile = tmpfile();
		fwrite($this->tmpFile, $data);
		rewind($this->tmpFile);
		
		$this->setConnectionOptions(array(
			CURLOPT_PUT => true,
			CURLOPT_INFILE => $this->tmpFile,
			CURLOPT_INFILESIZE => strlen($data),
		));
		return $this;
	}
		
	/**
	 * Prepare DELETE request
	 * @return HttpClient 
	 * @access protected
	 */
	protected function preDeleteRequest() {
		$this->setConnectionOptions(array(
			CURLOPT_CUSTOMREQUEST => 'DELETE',
		));
		return $this;
	}
	
	/**
	 * Number of requests left
	 * @return int
	 */
	public function requestsLeft() {
		$headers = $this->getHeaders();
		if (count($headers) == 0) {
			// No requests made yet
			return null;
		}		
		return $headers['X-RateLimit-Remaining'];
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
		$method = 'pre'.$requestMethod.'request';
		if (!method_exists($this, $method)) {
			throw new HttpClient_Exception('Request method not allowed');
		}
		
		$this
			->openConnection()
			->setConnectionOptions(array(
					CURLOPT_URL => $url
				)
			);
		
		if (!is_null($headers)) {
			$this->setConnectionOptions(array(
					CURLOPT_HTTPHEADER => $headers,
				)
			);
		}
		
		$this
			->$method($data)
			->exec()
			->closeConnection()
		;
		return $this->response;
	}
	
	/**
	 * Throws exceptions if httpStatus contains error status
	 * @throws HttpClient_HttpStatusException
	 * @throws HttpClient_UnknownHttpStatusException
	 * @access protected
	 */
	protected function handleError() {
		$okStatus = array(
			100, 200, 201,
		);
		$messages = array(
			401 => 'Authorization required',
			403 => 'Forbidden request',
			404 => 'Not found',
			406 => 'Not accepted',
			422 => 'Unprocessable entity',
			500 => 'Internal server error',
			501 => 'Method not implemented',
		);
		
		if (isset($messages[$this->httpStatus])) {
			throw new HttpClient_HttpStatusException($messages[$this->httpStatus], $this->httpStatus);
		}
		if (!in_array($this->httpStatus, $okStatus)) {
			throw new HttpClient_UnknownHttpStatusException('Unknown http status: ' . $this->httpStatus);
		}
	}
	
	/**
	 * Execute request
	 * Redirects via cURL option CURLOPT_FOLLOWLOCATION won't work if safe mode
	 * or open basedir is active
	 *
	 * @access protected
	 * @return HttpClient
	 * @throws HttpClient_HttpStatusException
	 * @throws HttpClient_UnknownHttpStatusException
	 * @throws HttpClient_ConnectionErrorException
	 */
	protected function exec() {
		static $loops = 0;
		static $maxLoops = 20;

		if ($loops++ >= $maxLoops) {
			$loops = 0;
			throw new HttpClient_ConnectionErrorException('Too many redirects in request');
		}

		$response = explode("\r\n\r\n", curl_exec($this->connection), 2);
		$this->httpStatus = curl_getinfo($this->connection, CURLINFO_HTTP_CODE);
		
		$header = isset($response[0]) ? $response[0] : '';
		$data   = isset($response[1]) ? $response[1] : '';

		// Ignore Continue header
		if ($header == "HTTP/1.1 100 Continue") {
			$response = explode("\r\n\r\n", $data, 2);
			$header = isset($response[0]) ? $response[0] : '';
			$data   = isset($response[1]) ? $response[1] : '';
		}
		
		$this->lastHeader = $header;
		$this->response = $data;
		
		if ($this->httpStatus == 301 || $this->httpStatus == 302) {
			$matches = array();
			preg_match('/Location:(.*?)\n/', $header, $matches);
			$url = @parse_url(trim(array_pop($matches)));
			if (!$url) {
				//couldn't process the url to redirect to
				$loops = 0;
				throw new HttpClient_ConnectionErrorException('Invalid redirect');
			}

			$this->setConnectionOptions(array(
				CURLOPT_URL => $url['scheme'] . '://' . $url['host'] . $url['path'] . (!empty($url['query']) ? '?' . $url['query'] : '')
			));

			$this->exec();
		}
		
		$loops = 0;
		
		$this->handleError();
		
		return $this;
	}
	
	/**
	 * Get last response
	 * 
	 * @return string
	 * @access public
	 */
	public function getLastResponse() {
		return $this->response;
	}
	
	/**
	 * Get headers
	 * 
	 * @return array
	 * @access public
	 */
	public function getHeaders() {
		$headers = array();
		if (preg_match_all('/([\w-]+): (.*)/', $this->lastHeader, $matches)) {
			for ($i = count($matches[0]) - 1; $i >= 0; $i--) {
				$headers[$matches[1][$i]] = $matches[2][$i];
			}
		}
		return $headers;
	}
}