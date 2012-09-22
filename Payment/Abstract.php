<?php

/*
 * Payment_Abstract class file
 */

namespace Moneybird;

/**
 * Payment_Abstract
 * @abstract
 */
abstract class Payment_Abstract extends Domainmodel_Abstract implements Mapper_Mapable {
	
	protected $createdAt;
	protected $id; 
	protected $paymentDate;
	protected $paymentMethod;
	protected $price;
	protected $updatedAt; 
	
	protected $_readonlyAttr = array(
		'createdAt',
		'creditInvoiceId',
		'id', 
		'incomingInvoiceId',
		'invoiceId',
		'updatedAt', 
	);
	
	/**
	 * Allowed payment methods
	 * @var Array
	 */
	protected $_paymentMethods = array(
		'bank_transfer',
		'creditcard',
		'credit_invoice',
		'direct_debit',
		'ideal',
		'paypal',
		'pin',
	);
	
	/**
	 * Set payment method
	 * @param string $value
	 * @throws Payment_InvalidMethodException
	 */
	protected function setPaymentMethodAttr($value) {
		if (!in_array($value, $this->_paymentMethods)) {
			throw new Payment_InvalidMethodException('Invalid payment method: ' . $value);
		}

		$this->paymentMethod = $value;
	}
	
}
