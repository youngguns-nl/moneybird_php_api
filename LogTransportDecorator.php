<?php

namespace Moneybird;

use Psr\Log\LoggerInterface;

class LogTransportDecorator implements Transport
{
	/**
	 * @var Transport
	 */
	protected $decorator;

	/**
	 * @var LoggerInterface
	 */
	protected $log;

	/**
	 * @param Transport $decorator
	 * @param LoggerInterface $log
	 */
	public function __construct(Transport $decorator, LoggerInterface $log)
	{
		$this->decorator = $decorator;
		$this->log = $log;
	}

	public function send($url, $requestMethod, $data = null, Array $headers = null)
	{
		$this->log->info(
			'MoneyBird Request',
			[
				'url' => $url,
				'requestMethod' => $requestMethod,
				'data' => $data,
				'headers' => $headers
			]
		);

		try {
			$response = $this->decorator->send($url, $requestMethod, $data, $headers);

			$context = ['response' => $response];

			$context = array_merge(
				$context,
				$this->getRequestLogContext()
			);

			$this->log->info('MoneyBird Response', $context);
		} catch (\Moneybird\HttpClient\Exception $e) {
			$context = ['exception' => $e];

			$context = array_merge(
				$context,
				$this->getRequestLogContext()
			);

			$this->log->error('Response failed', $context);

			throw $e;
		}

		return $response;
	}

	/**
	 * @return array
	 */
	protected function getRequestLogContext()
	{
		return $this->decorator->requestsLeft() === null
			? []
			: ['requestsLeft' => $this->decorator->requestsLeft()];
	}

	public function requestsLeft()
	{
		return $this->decorator->requestsLeft();
	}

	public function getLastResponse()
	{
		return $this->decorator->getLastResponse();
	}

	public function setUserAgent($userAgent)
	{
		return $this->decorator->setUserAgent($userAgent);
	}
}
