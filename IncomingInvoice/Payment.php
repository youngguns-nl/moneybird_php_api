<?php

/*
 * IncomingInvoice_Payment class file
 */
namespace Moneybird\IncomingInvoice;

use Moneybird\Payment\AbstractPayment;

/**
 * Payment
 */
class Payment extends AbstractPayment
{

    protected $incomingInvoiceId;
    protected $sendEmail;
}
