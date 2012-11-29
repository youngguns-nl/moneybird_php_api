<?php

/*
 * XML Mapper class
 */

namespace Moneybird;

/**
 * Translates XML into objects and objects into XML
 */
class XmlMapper implements Mapper {
		
	/**
	 * Mapper for xml element names to class names
	 * @var Array
	 */
	protected $objectMapper = array();
	
	
	/**
	 * Create XML mapper
	 */
	public function __construct() {
		$this->objectMapper = array(
			'contacts' => __NAMESPACE__.'\\Contact_Array',
			'contact'  => __NAMESPACE__.'\\Contact',
			
			'user' => __NAMESPACE__.'\\CurrentSession',

			'estimates'                => __NAMESPACE__.'\\Estimate_Array',
			'estimate'                 => __NAMESPACE__.'\\Estimate',
			'estimate/details'         => __NAMESPACE__.'\\Estimate_Detail_Array',
			'estimate/details/detail'  => __NAMESPACE__.'\\Estimate_Detail',
			'estimate/history'         => __NAMESPACE__.'\\Estimate_History_Array',
			'estimate/history/history' => __NAMESPACE__.'\\Estimate_History',
			
			'incoming-invoices'                 => __NAMESPACE__.'\\IncomingInvoice_Array',
			'incoming-invoice'                  => __NAMESPACE__.'\\IncomingInvoice',
			'incoming-invoice/details'          => __NAMESPACE__.'\\IncomingInvoice_Detail_Array',
			'incoming-invoice/details/detail'   => __NAMESPACE__.'\\IncomingInvoice_Detail',
			'incoming-invoice/payments'         => __NAMESPACE__.'\\IncomingInvoice_Payment_Array',
			'incoming-invoice/payments/payment' => __NAMESPACE__.'\\IncomingInvoice_Payment',
			'incoming-invoice/history'          => __NAMESPACE__.'\\IncomingInvoice_History_Array',
			'incoming-invoice/history/history'  => __NAMESPACE__.'\\IncomingInvoice_History',
			
			'invoices'                 => __NAMESPACE__.'\\Invoice_Array',
			'invoice'                  => __NAMESPACE__.'\\Invoice',
			'invoice/details'          => __NAMESPACE__.'\\Invoice_Detail_Array',
			'invoice/details/detail'   => __NAMESPACE__.'\\Invoice_Detail',
			'invoice/payments'         => __NAMESPACE__.'\\Invoice_Payment_Array',
			'invoice/payments/payment' => __NAMESPACE__.'\\Invoice_Payment',
			'invoice/history'          => __NAMESPACE__.'\\Invoice_History_Array',
			'invoice/history/history'  => __NAMESPACE__.'\\Invoice_History',
            'history'                  => __NAMESPACE__.'\\Invoice_History',
			
			'invoice-profiles' => __NAMESPACE__.'\\InvoiceProfile_Array',
			'invoice-profile'  => __NAMESPACE__.'\\InvoiceProfile',
			
			'products' => __NAMESPACE__.'\\Product_Array',
			'product'  => __NAMESPACE__.'\\Product',
			
			'recurring-templates'               => __NAMESPACE__.'\\RecurringTemplate_Array',
			'recurring-template'                => __NAMESPACE__.'\\RecurringTemplate',
			'recurring-template/details'        => __NAMESPACE__.'\\RecurringTemplate_Detail_Array',
			'recurring-template/details/detail' => __NAMESPACE__.'\\RecurringTemplate_Detail',
			
			'tax-rates' => __NAMESPACE__.'\\TaxRate_Array',
			'tax-rate'  => __NAMESPACE__.'\\TaxRate',
			
			'errors' => __NAMESPACE__.'\\Error_Array',
			'error'  => __NAMESPACE__.'\\Error',
		);
		uksort($this->objectMapper, array($this, 'sortKeyLength'));
	}
	
	/**
	 * Compare length of $a with length of $b
	 * @param string $a
	 * @param string $b
	 * @return int 
	 */
	protected function sortKeyLength($a, $b) {
		if (strlen($a) == strlen($b))
			return 0;
		if (strlen($a) > strlen($b))
			return -1;
		return 1;
	}
	
	/**
	 * Returns the content type of mapped objects
	 * 
	 * @return string
	 */
	public function getContentType() {
		return 'application/xml';
	}
	
	/**
	 * Create object from string
	 * @param string $string 
	 * @return Mapper_Mapable
	 * @access public
	 */
	public function mapFromStorage($string) {
		return $this->fromXmlString($string);
	}
	
	/**
	 * Map object
	 * @access public
	 * @param Mapper_Mapable $subject Object to map
	 * @return string
	 */
	public function mapToStorage(Mapper_Mapable $subject) {
		return $this->toXMLString($subject);
	}
	
	/**
	 * Create object from xml string
	 * @param string $xmlstring 
	 * @throws XmlMapper_InvalidXmlException
	 * @access public
	 */
	public function fromXmlString($xmlstring) {
		try {
			libxml_use_internal_errors(true);
			$xmlDoc = new SimpleXMLElement($xmlstring);
		} catch (\Exception $e) {
			throw new XmlMapper_InvalidXmlException('XML string could not be parsed');
		}
		
		return $this->fromXml($xmlDoc);
	}
	
	/**
	 * Create object from xml
	 * @param SimpleXMLElement $xmlElement
	 * @access public
	 */
	public function fromXml(SimpleXMLElement $xmlElement) {
		$classname = $this->xmlElementToClassname($xmlElement);
		if (!is_null($classname)) {
			$return = new $classname();
			if ($return instanceof ArrayObject) {				
				foreach ($xmlElement as $xmlChild) {
					$return->append($this->fromXml($xmlChild));
				}
			} else {
				$objectData = array();
				foreach ($xmlElement as $xmlChild) {
					$key = $this->xmlkeyToProperty($xmlChild->getName());
					if (isset($objectData[$key])) {
						if (!is_array($objectData[$key])) {
							$objectData[$key] = array($objectData[$key]);
						}
						$objectData[$key][] = $this->fromXml($xmlChild);
					} else {
						$objectData[$key] = $this->fromXml($xmlChild);
					}
				}
				$return = new $classname($objectData);
			}
		} else {
			$return = $this->castValue($xmlElement);
		}
		
		return $return;
	}

	/**
	 * Map XMLelement to classname
	 * @param SimpleXMLElement $xmlElement
	 * @return string
	 * @access protected
	 */
	protected function xmlElementToClassname(SimpleXMLElement $xmlElement) {
		$xpath = $xmlElement->getName();
		$parent = $xmlElement;
		while ($parent = current($parent->xpath('parent::*'))) {
			$xpath = $parent->getName() . '/' . $xpath;
		}
		
		foreach ($this->objectMapper as $key => $classname) {
			if (false !== strpos($xpath, $key) && $this->strEndsWith($xpath, $key)) {
				return $classname;
			}
		}
		return null;
	}
	
	/**
	 * Cast value based on type
	 * @param SimpleXMLElement $xmlElement
	 * @return mixed
	 * @access protected
	 */
	protected function castValue(SimpleXMLElement $xmlElement) {
		$attributes = $xmlElement->attributes();
		if (isset($attributes['nil']) && $attributes['nil'] == 'true') {
			$value = null;
		} else {
			switch (isset($attributes['type']) ? strval($attributes['type']) : null) {
				case 'integer':
					$value = intval($xmlElement);
					break;
				case 'float':
					$value = floatval($xmlElement);
					break;
				case 'boolean':
					$value = strval($xmlElement) == 'true';
					break;
				case 'datetime':
				case 'date':
					$value = new \DateTime(strval($xmlElement));
					break;
				case 'string':
				default:
					$value = strval($xmlElement);
					break;
			}
		}
		return $value;
	}
	
	/**
	 * Write xml key as property
	 * @param string $key
	 * @return string
	 * @access protected
	 */
	protected function xmlkeyToProperty($key) {
		return preg_replace_callback('/(-)(\w)/', array($this, 'xmlkeyToPropertyCallback'), $key);
	}
	
	/**
	 * Callback to rewrite xml key to property
	 * @param Array $match
	 * @return string
	 * @access protected
	 */
	protected function xmlkeyToPropertyCallback($match) {
		return strtoupper($match[2]);
	}
	
	/**
	 * Write property as xml key
	 * @param string $key
	 * @return string
	 * @access protected
	 */
	protected function propertyToXmlkey($key) {
		return strtolower(preg_replace_callback('/([a-z])([A-Z])/', array($this, 'propertyToXmlkeyCallback'), $key));
	}
	
	/**
	 * Callback to rewrite property to xml key
	 * @param Array $match
	 * @return string
	 * @access protected
	 */
	protected function propertyToXmlkeyCallback($match) {
		return $match[1].'-'.strtolower($match[2]);
	}
	
	/**
	 * Maps object to XML element-name
	 * @access protected
	 * @param Mapper_Mapable $subject Object to map to XML
	 * @return string
	 */
	protected function mapObjectToElementName(Mapper_Mapable $subject) {
		// Pick a default value based on the subject class
		$name = substr(get_class($subject), strlen(__NAMESPACE__) + 1);

		// See if the objectMapper array contains the (super)class
		foreach ($this->objectMapper as $key => $class) {
			if ($subject instanceof $class) {
				$name = $key;
				break;
			}
		}

		// Map the name to a proper XML key
		$name = $this->propertyToXmlkey($name);

		// Get rid of the unnecessary type specs (i.e. invoice_detail => detail)
		$simplified = array(
			'payment', 'history', 'detail',
		);
		foreach ($simplified as $simplify) {
			$name = preg_replace('/^[a-z\-\/]+[_\/]('.preg_quote($simplify).')/', '\\1', $name);
		}

		if ($subject instanceof SyncObject) {
			$name = 'ids';
		}
		
		$pos = ($subject instanceof SyncArray) ? strpos($name, '_') : strrpos($name, '_');
		if ($pos !== false) {
			$name = substr($name, 0, $pos);
		}
		if ($subject instanceof ArrayObject && substr($name, -1) != 's') {
			$name .= 's';
		}

		// Exceptions
		$name = str_replace(
			array(
				'historys',
				'details',
				'incoming-invoice',
			),
			array(
				'history',
				'details_attributes',
				'incoming_invoice',
			),
			$name
		);

		return lcfirst($name);
	}
	
	/** 
	 * Test if $str ends with $end
	 * @param string $str
	 * @param string $end
	 * @return bool
	 * @access protected
	 */
	protected function strEndsWith($str, $end) {
		return substr_compare($str, $end, -strlen($end), strlen($end)) === 0;
	}
	
	/**
	 * Convert to XML
	 *
	 * @access public
	 * @param Mapper_Mapable $subject Object to map to XML
	 * @return SimpleXMLElement
	 */
	public function toXML(Mapper_Mapable $subject) {
		$xmlRoot = new SimpleXMLElement('<' . $this->mapObjectToElementName($subject) . ' />');
		if ($subject instanceof ArrayObject && !($subject instanceof SyncArray)) {
			$xmlRoot->addAttribute('type', 'array');
		}
		if ($subject instanceof DeleteBySaving && $subject->isDeleted()) {
			$xmlRoot->addChild('_destroy', '1');
		}
		
		foreach ($subject->toArray() as $property => $value) {
			$key = $this->propertyToXmlkey($property);			
			if (is_null($value)) {
				$xmlRoot->addChild($key)->addAttribute('nil', 'true');
			} elseif (is_bool($value)) {
				$xmlRoot->addChild($key, $value === true ? 'true' : 'false');
			} elseif (is_array($value)) {
				foreach ($value as $v) {
					$xmlRoot->addChild($key, $v);
				}
			} elseif (!is_object($value) && !is_array($value)) {
				$xmlRoot->addChild($key, $value);
			} elseif (is_object($value) && $value instanceof Mapper_Mapable) {
				$xmlRoot->appendXML($this->toXml($value));
			} elseif (is_object($value) && $value instanceof \DateTime) {
				$xmlRoot->addChild($key, $value->format('c'));
			} else {
				throw new Mapper_Exception('Invalid value for key '.$key);
			}
		}

		return $xmlRoot;
	}
	
	/**
	 * Convert to XML string
	 *
	 * @access public
	 * @param Mapper_Mapable $subject Object to map to XML
	 * @return string
	 */
	public function toXMLString(Mapper_Mapable $subject) {
		return $this->toXML($subject)->asXml();
	}
}