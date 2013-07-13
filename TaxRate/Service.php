<?php

/*
 * TaxRate service class
 */

namespace Moneybird\TaxRate;

use Moneybird\Service as ServiceInterface;
use Moneybird\ApiConnector;
use Moneybird\InvalidFilterException;
use Moneybird\TaxRate;

/**
 * TaxRate service
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
	
	/**
	 * Get all tax rates
	 * 
	 * @param string $filter Filter name (all, sales, purchase, inactive)
	 * @return ArrayObject
	 * @throws InvalidFilterException 
	 */
	public function getAll($filter = null) {
		$filters = array('all', 'sales', 'purchase', 'inactive');
		if (!in_array($filter, $filters)) {
			$message = 'Unknown filter "' . $filter . '" for TaxRates';
			$message .= '; available filters: ' . implode(', ', $filters);
			throw new InvalidFilterException($message);
		}

		$rates = new ArrayObject;
		foreach ($this->connector->getAll(__NAMESPACE__) as $rate) {
			if (($filter == 'inactive' && $rate->active) || ($filter != 'inactive' && !$rate->active)) {
				continue;
			}
			if ($filter == 'sales' && $rate->taxRateType != TaxRate::RATE_TYPE_SALES) {
				continue;
			} elseif ($filter == 'purchase' && $rate->taxRateType != TaxRate::RATE_TYPE_PURCHASE) {
				continue;
			}
			$rates->append($rate);
		}
		return $rates;
	}
}