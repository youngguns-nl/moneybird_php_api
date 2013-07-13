<?php

/*
 * IncomingInvoice subject interface
 */

namespace Moneybird\IncomingInvoice;

use Moneybird\IncomingInvoice;

/**
 * IncomingInvoice subject is an object that has invoices (contact or estimate)
 */
interface Subject {
	
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
	 * @return ArrayObject
	 * @param Service $service
	 * @param string $filter
	 * @access public
	 */
	public function getIncomingInvoices(Service $service, $filter = null);
}

?>
