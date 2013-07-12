<?php

/*
 * Estimate subject interface
 */

namespace Moneybird\Estimate;

use Moneybird\Estimate;

/**
 * Estimate subject is an object that has invoices (contact or estimate)
 */
interface Subject {
	
	/**
	 * Create an invoice
	 *
	 * @return Estimate
	 * @access public
	 */
	public function createEstimate();

	/**
	 * Get all invoices of subject
	 *
	 * @return ArrayObject
	 * @param Service $service
	 * @param string $filter
	 * @access public
	 */
	public function getEstimates(Service $service, $filter = null);
}

?>
