<?php

/*
 * Estimate_Envelope class file
 */

namespace Moneybird;

/**
 * Estimate_Envelope
 */
class Estimate_Envelope extends Envelope_Abstract {
	
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
