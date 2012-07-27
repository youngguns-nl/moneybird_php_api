<?php

/*
 * Invoice_Envelope class file
 */

namespace Moneybird;

/**
 * Invoice_Envelope
 */
class Invoice_Envelope extends Envelope_Abstract {
	
	protected $invoiceEmail;
	
	/**
	 * Init discloseNotEmpty
	 *
	 * @access protected
	 */
	protected function initDiscloseNotEmpty() {
		$this->_discloseNotEmpty[] = 'invoiceId';
		$this->_discloseNotEmpty[] = 'invoiceEmail';
	}

}
