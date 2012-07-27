<?php

/*
 * InvoiceProfile service class
 */

namespace Moneybird;

/**
 * InvoiceProfile service
 */
class InvoiceProfile_Service implements Service {
	
	/**
	 * ApiConnector object
	 * @var ApiConnector
	 */
	protected $connector;
	
	public function __construct(ApiConnector $connector) {
		$this->connector = $connector;
	}
		
	public function getAll() {
		return $this->connector->getAll('InvoiceProfile');
	}	
}