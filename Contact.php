<?php

/*
 * Contact class
 */

namespace Moneybird;

/**
 * Contact
 */
class Contact 
	extends 
		Domainmodel_Abstract 
	implements 
		Mapper_Mapable, 
		Storable, 
		Invoice_Subject,
		RecurringTemplate_Subject,
		Estimate_Subject,
		IncomingInvoice_Subject {
		
	protected $address1; 
	protected $address2; 
	protected $attention; 
	protected $bankAccount; 
	protected $chamberOfCommerce; 
	protected $city; 
	protected $companyName; 
	protected $contactHash; 
	protected $contactName; 
	protected $country; 
	protected $createdAt; 
	protected $customerId; 
	protected $email; 
	protected $firstname; 
	protected $id; 
	protected $lastname; 
	protected $name; 
	protected $phone; 
	protected $revision; 
	protected $sendMethod; 
	protected $taxNumber; 
	protected $updatedAt; 
	protected $zipcode;
	
	protected $_readonlyAttr = array(
		'contactHash',
		'contactName',
		'createdAt',
		'id',
		'name',
		'revision',
		'updatedAt'
	);
	
	protected $_requiredAttr = array(
		//'customerId',
		array('companyName', 'firstname', 'lastname',),
	);
	
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
	 * Deletes a contact
	 * @param Service $service
	 */
	public function delete(Service $service) {
		$service->delete($this);
	}
	
	/**
	 * Updates or inserts a contact
	 * @param Service $service
	 * @return self
	 * @throws NotValidException
	 */
	public function save(Service $service) {
		if (!$this->validate()){
			throw new NotValidException('Unable to validate contact');
		}
		
		return $this->reload(
			$service->save($this)
		);
	}

	/**
	 * Copy the contact
	 * @return self
	 */
	public function copy() {
		return parent::copy(array(
			'customerId',
		));
	}
	
	/**
	 * Create an invoice for this contact
	 * @param array $data
	 * @return Invoice
	 */
	public function createInvoice(array $data = array()) {
		return new Invoice($data, $this, true);
	}
	
	/**
	 * Get all invoices of this contact
	 *
	 * @return Invoice_Array
	 * @param Invoice_Service $service
	 * @param string $filter
	 * @access public
	 */
	public function getInvoices(Invoice_Service $service, $filter = null) {
		return $service->getAll($filter, $this);
	}
	
	/**
	 * Create a recurring template for this contact
	 * @param array $data
	 * @return RecurringTemplate
	 */
	public function createRecurringTemplate(array $data = array()) {
		return new RecurringTemplate($data, $this, true);
	}
	
	/**
	 * Get all recurring templates of this contact
	 *
	 * @return RecurringTemplate_Array
	 * @param RecurringTemplate_Service $service
	 * @param string $filter
	 * @access public
	 */
	public function getRecurringTemplates(RecurringTemplate_Service $service, $filter = null) {
		return $service->getAll($filter, $this);
	}
	
	/**
	 * Create an estimate for this contact
	 * @param array $data
	 * @return Estimate
	 */
	public function createEstimate(array $data = array()) {
		return new Estimate($data, $this, true);
	}
	
	/**
	 * Get all estimates of this contact
	 *
	 * @return Estimate_Array
	 * @param Estimate_Service $service
	 * @param string $filter
	 * @access public
	 */
	public function getEstimates(Estimate_Service $service, $filter = null) {
		return $service->getAll($filter, $this);
	}
	
	/**
	 * Create an invoice for this contact
	 * @param array $data
	 * @return IncomingInvoice
	 */
	public function createIncomingInvoice(array $data = array()) {
		return new IncomingInvoice($data, $this, true);
	}
	
	/**
	 * Get all invoices of this contact
	 *
	 * @return IncomingInvoice_Array
	 * @param IncomingInvoice_Service $service
	 * @param string $filter
	 * @access public
	 */
	public function getIncomingInvoices(IncomingInvoice_Service $service, $filter = null) {
		return $service->getAll($filter, $this);
	}
}