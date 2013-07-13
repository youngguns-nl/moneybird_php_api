<?php

/*
 * Estimate class file
 */

namespace Moneybird;

use Moneybird\Domainmodel\AbstractModel;
use Moneybird\Mapper\Mapable;
use Moneybird\Estimate\Detail\ArrayObject as DetailArray;
use Moneybird\Estimate\History\ArrayObject as HistoryArray;
use Moneybird\Estimate\Service as EstimateService;

/**
 * Estimate
 */
class Estimate 
	extends 
		AbstractModel
	implements 
		Mapable, 
		Storable, 
		Sendable, 
		PdfDocument {
	
	protected $address1; 
	protected $address2; 
	protected $attention; 
	protected $city; 
	protected $companyName;
    protected $conceptId;
	protected $contactId;
	protected $country; 
	protected $createdAt;
	protected $currency;
	protected $customerId;
    protected $daysOpen;
	protected $discount;
	protected $dueDateInterval;
	protected $estimateDate;
	protected $estimateHash;
	protected $estimateId;
    protected $exchangeRate;
	protected $firstname; 
	protected $id; 
	protected $invoiceProfileId;
	protected $invoiceProfileVersionId;
	protected $language;
	protected $lastname;
    protected $poNumber;
	protected $postText;
    protected $preText;
    protected $pricesAreInclTax;
    protected $revision;
	protected $sendMethod; 
	protected $showCustomerId;
	protected $showTax;
	protected $signOnline;
	protected $state;
    protected $totalPriceExclTax;
    protected $totalPriceExclTaxBase;
	protected $totalPriceInclTax;
    protected $totalPriceInclTaxBase;
	protected $updatedAt; 
	protected $url;
	protected $zipcode;
	protected $details;
	protected $history;
	
	protected $pdfUrl;
	
	protected $_readonlyAttr = array(
        'conceptId',
		'createdAt',
        'daysOpen',
		'estimateHash',
		'id', 
		'invoiceProfileVersionId',
        'revision',
		'sendMethod', 
		'signOnline',
		'state',
        'totalPriceExclTax',
        'totalPriceExclTaxBase',
        'totalPriceInclTax',
        'totalPriceInclTaxBase',
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
	 * @param DetailArray $value
	 * @param bool $isDirty new value is dirty, defaults to true
	 */
	protected function setDetailsAttr(DetailArray $value = null, $isDirty = true) {
		if (!is_null($value)) {
			$this->details = $value;
			$this->setDirtyState($isDirty, 'details');
		}
	}
	
	/**
	 * Set history
	 * @param HistoryArray $value
	 * @param bool $isDirty new value is dirty, defaults to true
	 */
	protected function setHistoryAttr(HistoryArray $value = null, $isDirty = true) {
		if (!is_null($value)) {
			$this->history = $value;
			$this->setDirtyState($isDirty, 'history');
		}
	}
	
	/**
	 * Initialize vars 
	 */
	protected function _initVars() {
		$this->details = new DetailArray();
		$this->history = new HistoryArray();
		$this->estimateDate = new \DateTime();
		return parent::_initVars();
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
	 * @param EstimateService $service
	 * @return self 
	 */
	public function markAsSent(EstimateService $service) {
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
	 * @param bool $isDirty new data is dirty, defaults to true
	 * @return self
	 */
	public function setContact(Contact $contact, $isDirty = true) {
		$this->contactId = $contact->id;
		$this->setDirtyState($isDirty, 'contactId');
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
			$this->setDirtyState($isDirty, $property);
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
