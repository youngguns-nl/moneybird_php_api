<?php

/*
 * Payment class file
 */

namespace Moneybird\Invoice;

use Moneybird\Payment\AbstractPayment;

/**
 * Payment
 */
class Payment extends AbstractPayment {
	
	protected $creditInvoiceId;
	protected $invoiceId;
	protected $sendEmail;
	
}
