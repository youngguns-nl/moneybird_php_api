<?php

/**
 * Interface for MoneybirdRecurringTemplate
 *
 */
interface iMoneybirdRecurringTemplate extends iMoneybirdObject {

	/**
	 * Save template
	 *
	 * @return MoneybirdRecurringTemplate
	 * @access public
	 */
	public function save();

	/**
	 * Delete template
	 *
	 * @access public
	 */
	public function delete();

	/**
	 * Get all invoices created by template
	 *
	 * @return array
	 * @param string|iMoneybirdFilter $filter optional, filter to apply
	 * @access public
	 * @throws MoneybirdUnknownFilterException
	 */
	public function getInvoices($filter=null);
}

/**
 * Interface for MoneybirdRecurringTemplateDetail
 *
 */
interface iMoneybirdRecurringTemplateDetail extends iMoneybirdObject {
	
}

/**
 * RecurringTemplate in Moneybird
 *
 */
class MoneybirdRecurringTemplate extends MoneybirdObject implements iMoneybirdRecurringTemplate {
	
	/**
	 * Send frequency
	 *
	 * @const FREQUENCY_WEEKLY Send every week
	 */
	const FREQUENCY_WEEKLY = 1;
	
	/**
	 * Send frequency
	 *
	 * @const FREQUENCY_MONTH Send every month
	 */
	const FREQUENCY_MONTH = 2;

	/**
	 * Send frequency
	 *
	 * @const FREQUENCY_QUARTER Send every quarter
	 */
	const FREQUENCY_QUARTER = 3;

	/**
	 * Send frequency
	 *
	 * @const FREQUENCY_6MONTHS Send every 6 months
	 */
	const FREQUENCY_6MONTHS = 4;

	/**
	 * Send frequency
	 *
	 * @const FREQUENCY_YEAR Send every year
	 */
	const FREQUENCY_YEAR = 5;

	/**
	 * Load object from XML
	 *
	 * @access public
	 * @param SimpleXMLElement $xml
	 */
	public function fromXML(SimpleXMLElement $xml) {
		parent::fromXML($xml, array(
			'details' => 'MoneybirdRecurringTemplateLine',
		));
	}

	/**
	 * Convert object to XML
	 *
	 * @access public
	 * @return string
	 */
	public function toXML() {
		return parent::toXML(
				array(
				'details' => 'details_attributes',
				), '<recurring-template>', '</recurring-template>'
		);
	}

	/**
	 * Save template
	 *
	 * @return MoneybirdRecurringTemplate
	 * @access public
	 */
	public function save() {
		return $this->api->saveRecurringTemplate($this);
	}

	/**
	 * Delete template
	 *
	 * @access public
	 */
	public function delete() {
		$this->api->deleteRecurringTemplate($this);
	}

	/**
	 * Get all invoices created by template
	 *
	 * @return array
	 * @param string|iMoneybirdFilter $filter optional, filter to apply
	 * @access public
	 * @throws MoneybirdUnknownFilterException
	 */
	public function getInvoices($filter=null) {
		return $this->api->getInvoices($filter, $this);
	}

}

/**
 * RecurringTemplateLine in Moneybird
 *
 */
class MoneybirdRecurringTemplateLine extends MoneybirdObject implements iMoneybirdRecurringTemplateDetail {

	/**
	 * Line is marked for deletion
	 * @var bool
	 */
	protected $deleted = false;

	/**
	 * Load object from XML
	 *
	 * @access public
	 * @param SimpleXMLElement $xml
	 */
	public function fromXML(SimpleXMLElement $xml) {
		parent::fromXML($xml);
		$this->amount = $this->amount_plain;
	}

	/**
	 * Convert to XML string
	 *
	 * @access public
	 * @return string
	 */
	public function toXML() {
		$keyOpen = '<detail type="RecurringTemplateDetail"';
		if ($this->deleted) {
			$keyOpen .= ' _destroy="1"';
		}
		$keyOpen .= '>';

		return parent::toXML(
				null, $keyOpen, '</detail>', array(
				'total_price_excl_tax',
				'total_price_incl_tax',
				'amount_plain',
				)
		);
	}

	/**
	 * Mark line for deletion
	 *
	 * @access public
	 */
	public function delete() {
		$this->deleted = true;
	}

}
