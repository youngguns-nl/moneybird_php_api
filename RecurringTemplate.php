<?php

/*
 * RecurringTemplate class file
 */

namespace Moneybird;

/**
 * RecurringTemplate
 */
class RecurringTemplate 
	extends 
		Domainmodel_Abstract 
	implements 
		Mapper_Mapable, 
		Storable,
		Invoice_Subject {
	
	/**
	 * Send frequency
	 * @const FREQUENCY_WEEKLY Send every week
	 */
	const FREQUENCY_WEEKLY = 1;
	
	/**
	 * Send frequency
	 * @const FREQUENCY_MONTH Send every month
	 */
	const FREQUENCY_MONTH = 2;

	/**
	 * Send frequency
	 * @const FREQUENCY_QUARTER Send every quarter
	 */
	const FREQUENCY_QUARTER = 3;

	/**
	 * Send frequency
	 * @const FREQUENCY_6MONTHS Send every 6 months
	 */
	const FREQUENCY_6MONTHS = 4;

	/**
	 * Send frequency
	 * @const FREQUENCY_YEAR Send every year
	 */
	const FREQUENCY_YEAR = 5;
	
	/**
	 * Available frequency types
	 * @var Array
	 * @static
	 */
	public static $frequencyTypes = array(
		self::FREQUENCY_WEEKLY,
		self::FREQUENCY_MONTH,
		self::FREQUENCY_QUARTER,
		self::FREQUENCY_6MONTHS,
		self::FREQUENCY_YEAR,		
	);
	
	protected $active; 
	protected $contactId;
	protected $createdAt;
	protected $currency;
	protected $discount;
	protected $frequency; 
	protected $frequencyType; 
	protected $id; 
	protected $invoiceProfileId;
	protected $lastDate; 
	protected $nextDate; 
	protected $numberOfOccurences;
	protected $occurences;
	protected $sendInvoice;
	protected $startDate;
	protected $templateId;
	protected $totalPriceExclTax;
	protected $totalPriceInclTax;
	protected $updatedAt; 
	protected $details;
	
	protected $_readonlyAttr = array(
		'active',
		'createdAt',
		'id', 
		'lastDate',
		'nextDate',
		'numberOfOccurences',
		'totalPriceExclTax',
		'totalPriceInclTax',
		'updatedAt', 
	);
	
	protected $_requiredAttr = array(
		'contactId',
		'frequencyType',
	);
	
	/**
	 * Construct a new template
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
	 * Set frecuency type
	 * @param int $value
	 * @param bool $isDirty new value is dirty, defaults to true
	 * @throws Exception 
	 */
	protected function setFrequencyTypeAttr($value = null, $isDirty = true) {
		if (!is_null($value)) {
			if (!in_array($value, self::$frequencyTypes)) {
				throw new Exception('Invalid frequencyType');
			}
			$this->frequencyType = $value;
			$this->setDirtyState($isDirty, 'frequencyType');
		}
	}
	
	/**
	 * Set details
	 * @param RecurringTemplate_Detail_Array $value
	 * @param bool $isDirty new value is dirty, defaults to true
	 */
	protected function setDetailsAttr(RecurringTemplate_Detail_Array $value = null, $isDirty = true) {
		if (!is_null($value)) {
			$this->details = $value;
			$this->setDirtyState($isDirty, 'details');
		}
	}
	
	/**
	 * Initialize vars 
	 */
	protected function _initVars() {
		reset(self::$frequencyTypes);
		$this->details = new RecurringTemplate_Detail_Array();
		$this->frequencyType = current(self::$frequencyTypes);
		return parent::_initVars();
	}
	
	/**
	 * Copy info from contact to template
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
	 * Deletes a the template (or sets inactive)
	 * @param Service $service
	 */
	public function delete(Service $service) {
		$service->delete($this);
	}
	
	/**
	 * Updates or inserts the template
	 * @param Service $service
	 * @return self
	 * @throws NotValidException
	 */
	public function save(Service $service) {
		if (!$this->validate()){
			throw new NotValidException('Unable to validate template');
		}
		
		return $this->reload(
			$service->save($this)
		);
	}
	
	/**
	 * Create an invoice for this template
	 * @return Invoice
	 */
	public function createInvoice() {
		return new Invoice(array(), $this, true);
	}
	
	/**
	 * Get all invoices of this template
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
	 * Validate object
	 * @return bool
	 */
	protected function validate() {
		return count($this->details) > 0 && parent::validate();
	}
}