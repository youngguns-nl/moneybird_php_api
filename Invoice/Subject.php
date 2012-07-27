<?php

/*
 * Invoice subject interface
 */

namespace Moneybird;

/**
 * Invoice subject is an object that has invoices (contact or estimate)
 */
interface Invoice_Subject {
	
	/**
	 * Create an invoice
	 *
	 * @return Invoice
	 * @access public
	 */
	public function createInvoice();

	/**
	 * Get all invoices of subject
	 *
	 * @return Invoice_Array
	 * @param Invoice_Service $service
	 * @param string $filter
	 * @access public
	 */
	public function getInvoices(Invoice_Service $service, $filter = null);
}

?>
