<?php

/*
 * IncomingInvoice subject interface
 */

namespace Moneybird;

/**
 * IncomingInvoice subject is an object that has invoices (contact or estimate)
 */
interface IncomingInvoice_Subject {
	
	/**
	 * Create an invoice
	 *
	 * @return IncomingInvoice
	 * @access public
	 */
	public function createIncomingInvoice();

	/**
	 * Get all invoices of subject
	 *
	 * @return IncomingInvoice_Array
	 * @param IncomingInvoice_Service $service
	 * @param string $filter
	 * @access public
	 */
	public function getIncomingInvoices(IncomingInvoice_Service $service, $filter = null);
}

?>
