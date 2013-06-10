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
         * @param array $filter
         * @return self
         */
        public function copy(array $filter = array()) {
                return parent::copy(array(
                        'invoiceId',
                ));
        }

	
}
