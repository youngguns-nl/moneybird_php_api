<?php

/*
 * Invoice_Detail class file
 */

namespace Moneybird;

/**
 * Invoice_Detail
 */
class Invoice_Detail extends Detail_Abstract {
	
	protected $invoiceId;

	/**
	 * Copy the invoice detail
	 * @return self
	 */
	public function copy() {
		return parent::copy(array(
			'invoiceId',
		));
	}
	
}