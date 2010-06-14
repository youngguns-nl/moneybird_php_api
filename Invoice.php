<?php

/**
 * Interface for MoneybirdInvoice
 *
 */
interface iMoneybirdInvoice extends iMoneybirdObject
{
	/**
	 * Set a reference to the Api
	 *
	 * @param MoneybirdApi $api
	 * @access public
	 */
	public function setApi(MoneybirdApi $api);

	/**
	 * Copy info from contact to invoice
	 *
	 * @access public
	 * @param iMoneybirdContact $contact
	 */
	public function setContact(iMoneybirdContact $contact);
}

/**
 * Interface for MoneybirdInvoiceDetail
 *
 */
interface iMoneybirdInvoiceDetail extends iMoneybirdObject
{
}

/**
 * Interface for MoneybirdFilter
 *
 */
interface iMoneybirdFilter extends iMoneybirdObject
{
}

/**
 * Invoice in Moneybird
 *
 */
class MoneybirdInvoice extends MoneybirdObject implements iMoneybirdInvoice
{
	/**
	 * Api object
	 *
	 * @access private
	 * @var MoneybirdApi
	 */
	private $api;

	/**
	 * Set a reference to the Api
	 *
	 * @param MoneybirdApi $api
	 * @access public
	 */
	public function setApi(MoneybirdApi $api)
	{
		$this->api = $api;
	}

	/**
	 * Load object from XML
	 *
	 * @access public
	 * @param SimpleXMLElement $xml
	 */
	public function fromXML(SimpleXMLElement $xml)
	{
		parent::fromXML($xml, array(
			'history'  => 'MoneybirdInvoiceHistoryItem',
			'payments' => 'MoneybirdInvoicePayment',
			'details'  => 'MoneybirdInvoiceLine',
		));
		$this->pdf = $this->url.'.pdf';
	}

	/**
	 * Convert object to XML
	 *
	 * @access public
	 * @return string
	 */
	public function toXML()
	{
		return parent::toXML(
			array(
				'details' => 'details_attributes',
			),
			null,
			null,
			array('pdf', 'history', 'payments')
		);
	}

	/**
	 * Copy info from contact to invoice
	 *
	 * @access public
	 * @param iMoneybirdContact $contact
	 */
	public function setContact(iMoneybirdContact $contact)
	{
		$this->contact_id = $contact->id;
		foreach ($contact->getProperties() as $property => $value)
		{
			$this->$property = $value;
		}
	}

	/**
	 * Save invoice
	 *
	 * @return MoneybirdInvoice
	 * @access public
	 */
	public function save()
	{
		return $this->api->saveInvoice($this);
	}

	/**
	 * Delete invoice
	 *
	 * @access public
	 */
	public function delete()
	{
		$this->api->deleteInvoice($this);
	}

	/**
	 * Send an invoice
	 *
	 * @access public
	 * @param MoneybirdInvoiceSendInformation optional $sendinfo information to send invoice
	 */
	public function send(MoneybirdInvoiceSendInformation $sendinfo = null)
	{
		$this->api->sendInvoice($this, $sendinfo);
	}

	/**
	 * Send a reminder
	 *
	 * @access public
	 * @param MoneybirdInvoiceSendInformation optional $sendinfo information to send reminder
	 */
	public function remind(MoneybirdInvoiceSendInformation $sendinfo = null)
	{
		$this->api->sendInvoiceReminder($this, $sendinfo);
	}

	/**
	 * Mark invoice as send
	 *
	 * @access public
	 */
	public function markAsSent()
	{
		$this->send(new MoneybirdInvoiceSendInformation('hand'));
	}

	/**
	 * Register invoice payment
	 *
	 * @access public
	 * @param MoneybirdInvoicePayment $payment payment to register
	 */
	public function registerPayment(MoneybirdInvoicePayment $payment)
	{
		$this->api->registerInvoicePayment($this, $payment);
	}
}

/**
 * InvoiceDetail in Moneybird
 *
 */
class MoneybirdInvoiceDetail extends MoneybirdObject implements iMoneybirdInvoiceDetail
{
}

/**
 * InvoicePayment in Moneybird
 *
 */
class MoneybirdInvoicePayment extends MoneybirdInvoiceDetail
{
	/**
	 * Convert to XML string
	 *
	 * @access public
	 * @return string
	 */
	public function toXML()
	{
		return parent::toXML(
			null,
			'<invoice_payment>',
			'</invoice_payment>',
			array('invoice_id')
		);
	}
}

/**
 * InvoiceLine in Moneybird
 *
 */
class MoneybirdInvoiceLine extends MoneybirdInvoiceDetail
{
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
	public function fromXML(SimpleXMLElement $xml)
	{
		parent::fromXML($xml);
		$this->amount = $this->amount_plain;
	}

	/**
	 * Convert to XML string
	 *
	 * @access public
	 * @return string
	 */
	public function toXML()
	{
		$keyOpen  = '<detail type="InvoiceDetail"';
		if ($this->deleted)
		{
			$keyOpen .= ' _destroy="1"';
		}
		$keyOpen .= '>';

		return parent::toXML(
			null,
			$keyOpen,
			'</detail>',
			array(
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
	public function delete()
	{
		$this->deleted = true;
	}
}

/**
 * InvoiceHistoryItem in Moneybird
 *
 */
class MoneybirdInvoiceHistoryItem extends MoneybirdInvoiceDetail
{
}

/**
 * InvoiceSendInformation for Moneybird
 *
 */
class MoneybirdInvoiceSendInformation extends MoneybirdInvoiceDetail
{
	/**
	 * Constructor
	 *
	 * @access public
	 * @param string $sendMethod (email|post|hand)
	 * @param string $email Address to send to
	 * @param string $message Message in mail body
	 * @throws MoneybirdUnknownSendMethodException
	 */
	public function __construct($sendMethod='email', $email=null, $message=null)
	{
		if (!in_array($sendMethod, array('hand', 'email', 'post')))
		{
			throw new MoneybirdUnknownSendMethodException('Unknown send method: '.$sendMethod);
		}
		$this->properties['sendMethod'] = $sendMethod;

		if ($sendMethod == 'email')
		{
			$this->properties['email']   = $email;
			$this->properties['message'] = $message;
		}
	}

	/**
	 * Convert to XML string
	 *
	 * @access public
	 * @return string
	 */
	public function toXML()
	{
		$xml  = '<invoice>'.PHP_EOL;
		$xml .= '   <send-method>'.$this->properties['sendMethod'].'</send-method>'.PHP_EOL;
		if ($this->properties['email'] != null)
		{
			$xml .= '   <email>'.htmlspecialchars($this->properties['email']).'</email>'.PHP_EOL;
		}
		if ($this->properties['message'] != null)
		{
			$xml .= '   <invoice-email>'.htmlspecialchars($this->properties['message']).'</invoice-email>'.PHP_EOL;
		}
		$xml .= '</invoice>'.PHP_EOL;

		return $xml;
	}

	/**
	 * Load object from XML
	 *
	 * @access public
	 * @param SimpleXMLElement $xml
	 */
	public function fromXML(SimpleXMLElement $xml)
	{
		throw new Exception(__CLASS__.' cannot be loaded from XML');
	}
}

/**
 * Advanced filter for finding invoices
 *
 */
class MoneybirdAdvancedInvoiceFilter extends MoneybirdObject implements iMoneybirdFilter
{
	/**
	 * Constructor
	 *
	 * @access public
	 * @param DateTime $fromDate Start date
	 * @param DateTime $fromDate End date
	 * @param string|array $states States to search (draft|open|late|paid)
	 * @throws MoneybirdUnknownInvoiceStateException
	 */
	public function __construct(DateTime $fromDate, DateTime $toDate, $states)
	{
		$statesAvailable = array('draft', 'open', 'late', 'paid');

		$this->properties['from_date'] = $fromDate;
		$this->properties['to_date']   = $toDate;

		$this->properties['states'] = array();
		foreach ((array) $states as $state)
		{
			if (!in_array($state, $statesAvailable))
			{
				throw new MoneybirdUnknownInvoiceStateException('Unknown state for invoices: '.$state);
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
	public function fromXML(SimpleXMLElement $xml)
	{
		throw new Exception(__CLASS__.' cannot be loaded from XML');
	}

	/**
	 * Convert to XML string
	 *
	 * @access public
	 * @return string
	 */
	public function toXML()
	{
		return parent::toXML(
			null,
			'<filter>',
			'</filter>'
		);
	}
}
