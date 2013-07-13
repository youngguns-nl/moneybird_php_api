<?php

/*
 * Estimate_Envelope class file
 */

namespace Moneybird\Estimate;

use Moneybird\Envelope\AbstractEnvelope;

/**
 * Estimate_Envelope
 */
class Envelope extends AbstractEnvelope {
	
	protected $estimateEmail;
	
	/**
	 * Init discloseNotEmpty
	 *
	 * @access protected
	 */
	protected function _initDiscloseNotEmpty() {
		$this->_discloseNotEmpty[] = 'estimateEmail';
	}

}
