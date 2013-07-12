<?php

/*
 * Invoice_Envelope class file
 */

namespace Moneybird\Invoice;

use Moneybird\Envelope\AbstractEnvelope;

/**
 * Invoice_Envelope
 */
class Envelope extends AbstractEnvelope {
	
	protected $invoiceEmail;
	
	/**
	 * Init discloseNotEmpty
	 *
	 * @access protected
	 */
	protected function _initDiscloseNotEmpty() {
		$this->_discloseNotEmpty[] = 'invoiceId';
		$this->_discloseNotEmpty[] = 'invoiceEmail';
	}

}
