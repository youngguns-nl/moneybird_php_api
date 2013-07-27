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

    /**
     * Settle the payments
     *
     * @param Service $service
     * @param Payable $invoice
     * @param bool $sendEmail
     * @throws InvalidStateException
     * @throws UnableToSettleException
     */
    public function settle(Service $service, Payable $invoice, $sendEmail = false);
}
