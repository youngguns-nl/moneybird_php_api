<?php

/*
 * IncomingInvoice class file
 */

namespace Moneybird;

/**
 * IncomingInvoice
 */
class IncomingInvoice 
	extends 
		Domainmodel_Abstract 
	implements 
		Mapper_Mapable, 
		Storable, 
		Payable {
	
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
	public function __construct(array $data = array(), Contact $contact = null, $isDirty = true) {
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
	protected function setIdAttr($value) {
		if (!is_null($value) && !preg_match('/^[0-9]+$/D', $value)) {
			throw new InvalidIdException('Invalid id: ' . $value);
		}

		$this->id = $value;
	}
		
	/**
	 * Set details
	 * @param IncomingInvoice_Detail_Array $value
	 * @param bool $isDirty new value is dirty, defaults to true
	 */
	protected function setDetailsAttr(IncomingInvoice_Detail_Array $value = null, $isDirty = true) {
		if (!is_null($value)) {
			$this->details = $value;
			$this->setDirtyState($isDirty, 'details');
		}
	}
	
	/**
	 * Set payments
	 * @param IncomingInvoice_Payment_Array $value
	 * @param bool $isDirty new value is dirty, defaults to true
	 */
	protected function setPaymentsAttr(IncomingInvoice_Payment_Array $value = null, $isDirty = true) {
		if (!is_null($value)) {
			$this->payments = $value;
			$this->setDirtyState($isDirty, 'payments');
		}
	}
	
	/**
	 * Set history
	 * @param IncomingInvoice_History_Array $value
	 * @param bool $isDirty new value is dirty, defaults to true
	 */
	protected function setHistoryAttr(IncomingInvoice_History_Array $value = null, $isDirty = true) {
		if (!is_null($value)) {
			$this->history = $value;
			$this->setDirtyState($isDirty, 'history');
		}
	}
	
	/**
	 * Initialize vars 
	 */
	protected function _initVars() {
		$this->details = new IncomingInvoice_Detail_Array();
		$this->history = new IncomingInvoice_History_Array();
		$this->payments = new IncomingInvoice_Payment_Array();
		return parent::_initVars();
	}
	
	/**
	 * Register a payment for the invoice
	 * @param Service $service
	 * @param Payment_Abstract $payment
	 * @return self
	 */
	public function registerPayment(Service $service, Payment_Abstract $payment) {
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
	public function setContact(Contact $contact, $isDirty = true) {
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
	public function delete(Service $service) {
		$service->delete($this);
	}
	
	/**
	 * Updates or inserts an invoice
	 * @param Service $service
	 * @return self
	 * @throws NotValidException
	 */
	public function save(Service $service) {
		if (!$this->validate()){
			throw new NotValidException('Unable to validate invoice');
		}
		
		return $this->reload(
			$service->save($this)
		);
	}
	
	/**
	 * Validate object
	 * @return bool
	 */
	protected function validate() {
		return count($this->details) > 0 && parent::validate();
	}
}
