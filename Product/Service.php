<?php

/*
 * Product service class
 */

namespace Moneybird\Product;

use Moneybird\Service as ServiceInterface;
use Moneybird\ApiConnector;

/**
 * Product service
 */
class Service implements ServiceInterface {
	
	/**
	 * ApiConnector object
	 * @var ApiConnector
	 */
	protected $connector;
	
	public function __construct(ApiConnector $connector) {
		$this->connector = $connector;
	}
		
	public function getAll() {
		return $this->connector->getAll(__NAMESPACE__);
	}	
}