<?php

/*
 * IncomingInvoice class file
 */
namespace Moneybird;

use Moneybird\Domainmodel\AbstractModel;
use Moneybird\Mapper\Mapable;
use Moneybird\IncomingInvoice\Detail\ArrayObject as DetailArray;
use Moneybird\IncomingInvoice\Payment\ArrayObject as PaymentArray;
use Moneybird\IncomingInvoice\History\ArrayObject as HistoryArray;
use Moneybird\Payment\AbstractPayment;

/**
 * IncomingInvoice
 */
class IncomingInvoice extends
AbstractModel implements
Mapable, Storable, Payable
{

    protected $conceptId;
    protected $contactId;
    protected $createdAt;
    protected $currency;
    protected $dueDate;
    protected $id;
    protected $invoiceDate;
    protected $invoiceId;
    protected $revision;
    protected $state;
    protected $totalPaid;
    protected $totalUnpaid;
    protected $updatedAt;
    protected $details;
    protected $history;
    protected $payments;
    protected $_readonlyAttr = array(
        'conceptId',
        'createdAt',
        'id',
        'revision',
        'state',
        'totalPaid',
        'totalUnpaid',
        'updatedAt',
        'history',
        'payments',
    );
    protected $_requiredAttr = array(
        'contactId',
        'invoiceDate',
        'invoiceId',
    );

    /**
     * Construct a new invoice
     *
     * @param array $data
     * @param Contact $contact
     * @param bool $isDirty new data is dirty, defaults to true
     */
    public function __construct(array $data = array(), Contact $contact = null, $isDirty = true)
    {
        parent::__construct();
        if ($contact !== null) {
            $this->setContact($contact, $isDirty);
        }
        $this->setData($data, $isDirty);
    }

    /**
     * Set Id
     * @param int $value
     * @throws InvalidIdException
     */
    protected function setIdAttr($value)
    {
        if (!is_null($value) && !preg_match('/^[0-9]+$/D', $value)) {
            throw new InvalidIdException('Invalid id: ' . $value);
        }

        $this->id = $value;
    }

    /**
     * Set details
     * @param DetailArray $value
     * @param bool $isDirty new value is dirty, defaults to true
     */
    protected function setDetailsAttr(DetailArray $value = null, $isDirty = true)
    {
        if (!is_null($value)) {
            $this->details = $value;
            $this->setDirtyState($isDirty, 'details');
        }
    }

    /**
     * Set payments
     * @param PaymentArray $value
     * @param bool $isDirty new value is dirty, defaults to true
     */
    protected function setPaymentsAttr(PaymentArray $value = null, $isDirty = true)
    {
        if (!is_null($value)) {
            $this->payments = $value;
            $this->setDirtyState($isDirty, 'payments');
        }
    }

    /**
     * Set history
     * @param HistoryArray $value
     * @param bool $isDirty new value is dirty, defaults to true
     */
    protected function setHistoryAttr(HistoryArray $value = null, $isDirty = true)
    {
        if (!is_null($value)) {
            $this->history = $value;
            $this->setDirtyState($isDirty, 'history');
        }
    }

    /**
     * Initialize vars
     */
    protected function _initVars()
    {
        $this->details = new DetailArray();
        $this->history = new HistoryArray();
        $this->payments = new PaymentArray();
        return parent::_initVars();
    }

    /**
     * Register a payment for the invoice
     * @param Service $service
     * @param AbstractPayment $payment
     * @return self
     */
    public function registerPayment(Service $service, AbstractPayment $payment)
    {
        return $this->reload(
                $service->registerPayment($this, $payment)
        );
    }

    /**
     * Copy info from contact to invoice
     *
     * @access public
     * @param Contact $contact
     * @param bool $isDirty new data is dirty, defaults to true
     * @return self
     */
    public function setContact(Contact $contact, $isDirty = true)
    {
        $this->contactId = $contact->id;
        $this->setDirtyState($isDirty, 'contactId');
        $properties = array();
        foreach ($properties as $property) {
            $this->$property = $contact->$property;
            $this->setDirtyState($isDirty, $property);
        }
        return $this;
    }

    /**
     * Deletes an invoice
     * @param Service $service
     */
    public function delete(Service $service)
    {
        $service->delete($this);
    }

    /**
     * Updates or inserts an invoice
     * @param Service $service
     * @return self
     * @throws NotValidException
     */
    public function save(Service $service)
    {
        if (!$this->validate()) {
            throw new NotValidException('Unable to validate invoice');
        }

        return $this->reload(
                $service->save($this)
        );
    }

    /**
     * Settle the payments
     *
     * @param Service $service
     * @param Payable $invoice
     * @throws InvalidStateException
     * @throws UnableToSettleException
     */
    public function settle(Service $service, Payable $invoice, $sendEmail = false)
    {
        return $service->settle($this, $invoice);
    }

    /**
     * Validate object
     * @return bool
     */
    protected function validate()
    {
        return count($this->details) > 0 && parent::validate();
    }
}
