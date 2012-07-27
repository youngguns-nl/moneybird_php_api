<?php

/*
 * Estimate subject interface
 */

namespace Moneybird;

/**
 * Estimate subject is an object that has invoices (contact or estimate)
 */
interface Estimate_Subject {
	
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
	 * @return Estimate_Array
	 * @param Estimate_Service $service
	 * @param string $filter
	 * @access public
	 */
	public function getEstimates(Estimate_Service $service, $filter = null);
}

?>
