<?php

/*
 * AbstractPayment class file
 */
namespace Moneybird\Payment;

use Moneybird\Domainmodel\AbstractModel;
use Moneybird\Mapper\Mapable;

/**
 * AbstractPayment
 * @abstract
 */
abstract class AbstractPayment extends AbstractModel implements Mapable
{

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
        '',
        'bank_transfer',
        'cash',
        'creditcard',
        'credit_invoice',
        'direct_debit',
        'ideal',
        'paypal',
        'pin',
        'reversal',
    );

    /**
     * Set payment method
     * @param string $value
     * @param bool $isDirty new value is dirty, defaults to true
     * @throws InvalidMethodException
     */
    protected function setPaymentMethodAttr($value, $isDirty = true)
    {
        if ($value !== null && $value !== '' && !in_array($value, $this->_paymentMethods)) {
            throw new InvalidMethodException('Invalid payment method: ' . $value);
        }

        $this->paymentMethod = $value;
        $this->setDirtyState($isDirty, 'paymentMethod');
    }
}
