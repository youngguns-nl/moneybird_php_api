<?php

/**
 * Interface for MoneybirdEstimate
 *
 */
interface iMoneybirdEstimate extends iMoneybirdObject {

	/**
	 * Copy info from contact to estimate
	 *
	 * @access public
	 * @param iMoneybirdContact $contact
	 */
	public function setContact(iMoneybirdContact $contact);

	/**
	 * Save estimate
	 *
	 * @return MoneybirdEstimate
	 * @access public
	 */
	public function save();

	/**
	 * Delete estimate
	 *
	 * @access public
	 */
	public function delete();

	/**
	 * Send an estimate
	 *
	 * @access public
	 * @param MoneybirdEstimateSendInformation optional $sendinfo information to send estimate
	 */
	public function send(MoneybirdEstimateSendInformation $sendinfo = null);

	/**
	 * Mark estimate as send
	 *
	 * @access public
	 */
	public function markAsSent();

	/**
	 * Get estimate as PDF
	 *
	 * @access public
	 */
	public function getPdf();
}

/**
 * Interface for MoneybirdEstimateDetail
 *
 */
interface iMoneybirdEstimateDetail extends iMoneybirdObject {
	
}

/**
 * Estimate in Moneybird
 *
 */
class MoneybirdEstimate extends MoneybirdObject implements iMoneybirdEstimate {

	/**
	 * Load object from XML
	 *
	 * @access public
	 * @param SimpleXMLElement $xml
	 */
	public function fromXML(SimpleXMLElement $xml) {
		parent::fromXML($xml, array(
			'history' => 'MoneybirdEstimateHistoryItem',
			'details' => 'MoneybirdEstimateLine',
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
				), null, null, array('history',)
		);
	}

	/**
	 * Copy info from contact to estimate
	 *
	 * @access public
	 * @param iMoneybirdContact $contact
	 */
	public function setContact(iMoneybirdContact $contact) {
		$this->contact_id = $contact->id;
		foreach ($contact->getProperties() as $property => $value) {
			$this->$property = $value;
		}
	}

	/**
	 * Save estimate
	 *
	 * @return MoneybirdEstimate
	 * @access public
	 */
	public function save() {
		return $this->api->saveEstimate($this);
	}

	/**
	 * Delete estimate
	 *
	 * @access public
	 */
	public function delete() {
		$this->api->deleteEstimate($this);
	}

	/**
	 * Send an estimate
	 *
	 * @access public
	 * @param MoneybirdEstimateSendInformation optional $sendinfo information to send estimate
	 */
	public function send(MoneybirdEstimateSendInformation $sendinfo = null) {
		$this->api->sendEstimate($this, $sendinfo);
	}

	/**
	 * Mark estimate as send
	 *
	 * @access public
	 */
	public function markAsSent() {
		$this->send(new MoneybirdEstimateSendInformation('hand'));
	}

	/**
	 * Get estimate as PDF
	 *
	 * @access public
	 */
	public function getPdf() {
		return $this->api->getEstimatePdf($this);
	}

}

/**
 * EstimateDetail in Moneybird
 *
 */
class MoneybirdEstimateDetail extends MoneybirdObject implements iMoneybirdEstimateDetail {
	
}

/**
 * EstimateLine in Moneybird
 *
 */
class MoneybirdEstimateLine extends MoneybirdEstimateDetail {

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
		$keyOpen = '<detail type="EstimateDetail"';
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

/**
 * EstimateHistoryItem in Moneybird
 *
 */
class MoneybirdEstimateHistoryItem extends MoneybirdEstimateDetail {
	
}

/**
 * EstimateSendInformation for Moneybird
 *
 */
class MoneybirdEstimateSendInformation extends MoneybirdEstimateDetail {

	/**
	 * Constructor
	 *
	 * @access public
	 * @param string $sendMethod (email|post|hand)
	 * @param string $email Address to send to
	 * @param string $message Message in mail body
	 * @throws MoneybirdUnknownSendMethodException
	 */
	public function __construct($sendMethod='email', $email=null, $message=null) {
		if (!in_array($sendMethod, array('hand', 'email', 'post'))) {
			throw new MoneybirdUnknownSendMethodException('Unknown send method: ' . $sendMethod);
		}
		$this->properties['sendMethod'] = $sendMethod;

		if ($sendMethod == 'email') {
			$this->properties['email'] = $email;
			$this->properties['message'] = $message;
		}
	}

	/**
	 * Convert to XML string
	 *
	 * @access public
	 * @return string
	 */
	public function toXML() {
		$xml = '<estimate>' . PHP_EOL;
		$xml .= '   <send-method>' . $this->properties['sendMethod'] . '</send-method>' . PHP_EOL;
		if (!empty($this->properties['email'])) {
			$xml .= '   <email>' . htmlspecialchars($this->properties['email']) . '</email>' . PHP_EOL;
		}
		if (!empty($this->properties['message'])) {
			$xml .= '   <estimate-email>' . htmlspecialchars($this->properties['message']) . '</estimate-email>' . PHP_EOL;
		}
		$xml .= '</estimate>' . PHP_EOL;

		return $xml;
	}

	/**
	 * Load object from XML
	 *
	 * @access public
	 * @param SimpleXMLElement $xml
	 */
	public function fromXML(SimpleXMLElement $xml) {
		throw new Exception(__CLASS__ . ' cannot be loaded from XML');
	}

}

/**
 * Advanced filter for finding estimates
 *
 */
class MoneybirdAdvancedEstimateFilter extends MoneybirdObject implements iMoneybirdFilter {

	/**
	 * Constructor
	 *
	 * @access public
	 * @param DateTime $fromDate Start date
	 * @param DateTime $fromDate End date
	 * @param string|array $states States to search (draft|open|late|paid)
	 * @throws MoneybirdUnknownEstimateStateException
	 */
	public function __construct(DateTime $fromDate, DateTime $toDate, $states) {
		$statesAvailable = array('draft', 'open', 'accepted', 'rejected', 'billed', 'deleted');

		$this->properties['from_date'] = $fromDate;
		$this->properties['to_date'] = $toDate;

		$this->properties['states'] = array();
		foreach ((array) $states as $state) {
			if (!in_array($state, $statesAvailable)) {
				throw new MoneybirdUnknownEstimateStateException('Unknown state for estimates: ' . $state);
			}
			$this->properties['states'][] = $state;
		}
	}

	/**
	 * Load object from XML
	 *
	 * @access public
	 * @param SimpleXMLElement $xml
	 */
	public function fromXML(SimpleXMLElement $xml) {
		throw new Exception(__CLASS__ . ' cannot be loaded from XML');
	}

	/**
	 * Convert to XML string
	 *
	 * @access public
	 * @return string
	 */
	public function toXML() {
		return parent::toXML(
				null, '<filter>', '</filter>'
		);
	}

}
