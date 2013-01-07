<?php

/*
 * Estimate class file
 */

namespace Moneybird;

/**
 * Estimate
 */
class Estimate 
	extends 
		Domainmodel_Abstract 
	implements 
		Mapper_Mapable, 
		Storable, 
		Sendable, 
		PdfDocument {
	
	protected $address1; 
	protected $address2; 
	protected $attention; 
	protected $city; 
	protected $companyName; 
	protected $contactId;
	protected $country; 
	protected $createdAt;
	protected $currency;
	protected $customerId; 
	protected $discount;
	protected $dueDateInterval;
	protected $estimateDate;
	protected $estimateHash;
	protected $estimateId;
	protected $firstname; 
	protected $id; 
	protected $invoiceProfileId;
	protected $invoiceProfileVersionId;
	protected $language;
	protected $lastname; 
	protected $preText; 
	protected $postText; 
	protected $sendMethod; 
	protected $showCustomerId;
	protected $showTax;
	protected $signOnline;
	protected $state;
	protected $updatedAt; 
	protected $url;
	protected $zipcode;
	protected $details;
	protected $history;
	
	protected $pdfUrl;
	
	protected $_readonlyAttr = array(
		'createdAt',
		'estimateHash',
		'id', 
		'invoiceProfileVersionId',
		'sendMethod', 
		'signOnline',
		'state',
		'updatedAt', 
		'url',
		'history',
		
		'pdfUrl',
	);
	
	protected $_requiredAttr = array(
		array('contactId', 'companyName', 'firstname', 'lastname'),
		'estimateDate',
	);
	
	/**
	 * Construct a new estimate
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
	 * Set url
	 * @param string $value 
	 */
	protected function setUrlAttr($value = null) {
		if (!is_null($value)) {
			$this->url = $value;
			$this->pdfUrl = $value.'.pdf';
		}
	}
	
	/**
	 * Set details
	 * @param Estimate_Detail_Array $value 
	 */
	protected function setDetailsAttr(Estimate_Detail_Array $value = null) {
		if (!is_null($value)) {
			$this->details = $value;
		}
	}
	
	/**
	 * Set history
	 * @param Estimate_History_Array $value 
	 */
	protected function setHistoryAttr(Estimate_History_Array $value = null) {
		if (!is_null($value)) {
			$this->history = $value;
		}
	}
	
	/**
	 * Initialize vars 
	 */
	protected function _initVars() {
		$this->details = new Estimate_Detail_Array();
		$this->history = new Estimate_History_Array();
		$this->estimateDate = new \DateTime();
	}
	
	/**
	 * Send the estimate
	 * @param Service $service
	 * @param string $method Send method (email|hand|post); default: email
	 * @param type $email Address to send to; default: contact e-mail
	 * @param type $message
	 * @return self 
	 */
	public function send(Service $service, $method='email', $email=null, $message=null) {
		return $this->reload(
			$service->send($this, $method, $email, $message)
		);
	}
	
	/**
	 * Mark the estimate as sent
	 * @param Estimate_Service $service
	 * @return self 
	 */
	public function markAsSent(Estimate_Service $service) {
		return $this->send($service, 'hand');
	}
	
	/**
	 * Get the raw PDF content
	 * @param Service $service
	 * @return string
	 * @throws InvalidStateException
	 */
	public function getPdf(Service $service) {
		if ($this->state == 'draft') {
			throw new InvalidStateException('Send estimate before requesting PDF document');
		}
		return $service->getPdf($this);
	}
	
	/**
	 * Copy info from contact to estimate
	 *
	 * @access public
	 * @param Contact $contact
	 * @return self
	 */
	public function setContact(Contact $contact) {
		$this->contactId = $contact->id;
		$properties = array(
			'address1', 
			'address2', 
			'attention', 
			'city', 
			'companyName', 
			'country', 
			'customerId', 
			'firstname', 
			'lastname', 
			'zipcode',
		);
		foreach ($properties as $property) {
			$this->$property = $contact->$property;
		}
		return $this;
	}
	
	/**
	 * Deletes an estimate
	 * @param Service $service
	 */
	public function delete(Service $service) {
		$service->delete($this);
	}
	
	/**
	 * Updates or inserts an estimate
	 * @param Service $service
	 * @return self
	 * @throws NotValidException
	 */
	public function save(Service $service) {
		if (!$this->validate()){
			throw new NotValidException('Unable to validate estimate');
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
