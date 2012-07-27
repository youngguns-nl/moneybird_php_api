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
	 */
	public function __construct(array $data = array(), Contact $contact = null) {
		if (!is_null($contact)) {
			$this->setContact($contact);
		}
		parent::__construct($data);
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
	 */
	protected function setDetailsAttr(IncomingInvoice_Detail_Array $value = null) {
		if (!is_null($value)) {
			$this->details->merge($value);
		}
	}
	
	/**
	 * Set payments
	 * @param IncomingInvoice_Payment_Array $value 
	 */
	protected function setPaymentsAttr(IncomingInvoice_Payment_Array $value = null) {
		if (!is_null($value)) {
			$this->payments->merge($value);
		}
	}
	
	/**
	 * Set history
	 * @param IncomingInvoice_History_Array $value 
	 */
	protected function setHistoryAttr(IncomingInvoice_History_Array $value = null) {
		if (!is_null($value)) {
			$this->history->merge($value);
		}
	}
	
	/**
	 * Initialize vars 
	 */
	protected function _initVars() {
		$this->details = new IncomingInvoice_Detail_Array();
		$this->history = new IncomingInvoice_History_Array();
		$this->payments = new IncomingInvoice_Payment_Array();
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
	 * @return self
	 */
	public function setContact(Contact $contact) {
		$this->contactId = $contact->id;
		$properties = array();
		foreach ($properties as $property) {
			$this->$property = $contact->$property;
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
