<?php

/*
 * Product service class
 */

namespace Moneybird;

/**
 * Product service
 */
class Product_Service implements Service {
	
	/**
	 * ApiConnector object
	 * @var ApiConnector
	 */
	protected $connector;
	
	public function __construct(ApiConnector $connector) {
		$this->connector = $connector;
	}
		
	public function getAll() {
		return $this->connector->getAll('Product');
	}	
}