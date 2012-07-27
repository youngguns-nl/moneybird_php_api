<?php

/*
 * RecurringTemplate subject interface
 */

namespace Moneybird;

/**
 * RecurringTemplate subject is an object that has invoices (contact or estimate)
 */
interface RecurringTemplate_Subject {
	
	/**
	 * Create an invoice
	 *
	 * @return RecurringTemplate
	 * @access public
	 */
	public function createRecurringTemplate();

	/**
	 * Get all invoices of subject
	 *
	 * @return RecurringTemplate_Array
	 * @param RecurringTemplate_Service $service
	 * @param string $filter
	 * @access public
	 */
	public function getRecurringTemplates(RecurringTemplate_Service $service, $filter = null);
}

?>
