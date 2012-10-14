<?php

/*
 * Interface for Payable objects
 */

namespace Moneybird;

/**
 * Payable
 */
interface Payable {
	/**
	 * Register a payment for the invoice
	 * @param Service $service
	 * @param Payment_Abstract $payment
	 * @return self
	 */
	public function registerPayment(Service $service, Payment_Abstract $payment);
}
