<?php

/*
 * Interface for Payable objects
 */
namespace Moneybird;

use Moneybird\Payment\AbstractPayment;

/**
 * Payable
 */
interface Payable
{

    /**
     * Register a payment for the invoice
     * @param Service $service
     * @param AbstractPayment $payment
     * @return self
     */
    public function registerPayment(Service $service, AbstractPayment $payment);
}
