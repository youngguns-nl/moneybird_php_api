<?php

/**
 * Interface for MoneybirdObject
 *
 */
interface iMoneybirdObject {

	/**
	 * Set a reference to the Api
	 *
	 * @param MoneybirdApi $api
	 * @access public
	 */
	public function setApi(MoneybirdApi $api);

	/**
	 * Fill with XML
	 *
	 * @access public
	 * @param SimpleXMLElement $xml
	 */
	public function fromXML(SimpleXMLElement $xml);

	/**
	 * Convert to XML string
	 *
	 * @access public
	 * @return string
	 */
	public function toXML();
}

/**
 * Object in Moneybird
 *
 * @abstract
 */
abstract class MoneybirdObject implements iMoneybirdObject {

	/**
	 * Api object
	 *
	 * @access protected
	 * @var MoneybirdApi
	 */
	protected $api;

	/**
	 * Properties
	 *
	 * @access protected
	 * @var array
	 */
	protected $properties;

	/**
	 * Set a reference to the Api
	 *
	 * @param MoneybirdApi $api
	 * @access public
	 */
	public function setApi(MoneybirdApi $api) {
		$this->api = $api;
	}

	/**
	 * Set property
	 *
	 * @access public
	 * @param string $property
	 * @param mixed $value
	 * @throws Exception
	 */
	public function __set($property, $value) {
		$this->properties[$property] = $value;
	}

	/**
	 * Get property
	 *
	 * @access public
	 * @param string $property
	 * @return mixed
	 * @throws Exception
	 */
	public function __get($property) {
		if (!isset($this->properties[$property])) {
			return null;
		}
		return $this->properties[$property];
	}

	/**
	 * Load object from XML
	 *
	 * @access public
	 * @param SimpleXMLElement $xml XML to load
	 * @param array $arrayHandlers Mapping for converting array elements to objects
	 */
	public function fromXML(SimpleXMLElement $xml, Array $arrayHandlers = null) {
		foreach ($xml as $key => $value) {
			$attributes = $value->attributes();
			$type = isset($attributes['type']) ? $attributes['type'] : 'string';

			$key = str_replace('-', '_', $key);

			if (isset($attributes['nil']) && $attributes['nil'] == 'true') {
				$this->properties[$key] = null;
			} elseif ($type == 'array') {
				$this->properties[$key] = array();
				if (isset($arrayHandlers[$key])) {
					foreach ($value as $subxml) {
						$object = new $arrayHandlers[$key]($this);
						$object->fromXML($subxml);
						$this->properties[$key][] = $object;
					}
				}
			} elseif ($type == 'integer') {
				$this->properties[$key] = intval($value);
			} elseif ($type == 'float') {
				$this->properties[$key] = floatval($value);
			} elseif ($type == 'boolean') {
				$this->properties[$key] = $value == 'true';
			} elseif ($type == 'datetime' || $type == 'date') {
				$this->properties[$key] = new DateTime(strval($value));
			} else {
				$this->properties[$key] = strval($value);
			}
		}
	}

	/**
	 * Convert to XML string
	 *
	 * @access public
	 * @param array $arrayHandlers Mapping for internal naming of properties to XML tags
	 * @param string $elmKeyOpen Open tag
	 * @param string $elmKeyClose Close tag
	 * @param array $skipProperties Skip the properties with these names
	 * @return string
	 * @throws MoneybirdUnknownTypeException
	 */
	public function toXML(Array $arrayHandlers = null, $elmKeyOpen = null, $elmKeyClose = null, array $skipProperties = array()) {
		if ($this instanceof iMoneybirdContact) {
			$root = 'contact';
		} elseif ($this instanceof iMoneybirdInvoice) {
			$root = 'invoice';
		} elseif ($this instanceof iMoneybirdEstimate) {
			$root = 'estimate';
		} elseif ($this instanceof iMoneybirdRecurringTemplate) {
			$root = 'recurringtemplate';
		} else {
			// Guess
			$root = strtolower(substr(get_class($this), 9));
			//throw new MoneybirdUnknownTypeException('Unknown type: '.get_class($this));
		}

		if ($elmKeyOpen == null) {
			$elmKeyOpen = '<' . $root . '>';
		}
		if ($elmKeyClose == null) {
			$elmKeyClose = '</' . $root . '>';
		}

		$skipProperties = array_merge($skipProperties, array(
			'updated_at', 'created_at',
			));

		$xml = $elmKeyOpen . PHP_EOL;
		foreach ($this->properties as $key => $value) {
			if (in_array($key, $skipProperties)) {
				continue;
			}

			$key = str_replace('_', '-', $key);

			$keyOpen = '<' . $key . '>';
			$keyClose = '</' . $key . '>';

			if (is_object($value) && $value instanceof DateTime) {
				$value = $value->format('c');
			} elseif (is_array($value)) {
				if (isset($arrayHandlers[$key])) {
					$keyOpen = '<' . $arrayHandlers[$key] . ' type="array">' . PHP_EOL;
					$keyClose = '</' . $arrayHandlers[$key] . '>';
					$newvalue = '';
					foreach ($value as $object) {
						$newvalue .= $object->toXML();
					}
					$value = $newvalue;
				} else {
					foreach ($value as $arrayValue) {
						$xml .= '   ' . $keyOpen . htmlspecialchars($arrayValue) . $keyClose . PHP_EOL;
					}
					continue;
				}
			} elseif (is_null($value)) {
				$keyOpen = substr($keyOpen, 0, -1) . ' nil="true">';
			} elseif (is_bool($value)) {
				$value = $value === true ? 'true' : 'false';
			} else {
				$value = htmlspecialchars($value);
			}
			$xml .= '   ' . $keyOpen . $value . $keyClose . PHP_EOL;
		}
		$xml .= $elmKeyClose . PHP_EOL;

		return $xml;
	}

}

/**
 * Sync class
 *
 */
class MoneybirdSync extends MoneybirdObject {

	/**
	 * Type of sync object
	 * @var string
	 */
	protected $type;

	/**
	 * Array of id's
	 * @var array
	 */
	protected $ids;

	/**
	 * Create the sync object
	 *
	 * @access public
	 * @param string $type (contact|invoice|estimate|recurringTemplate)
	 * @param array $ids Array of ids
	 */
	public function __construct($type, array $ids) {
		$this->type = $type;
		$this->ids = $ids;
	}

	/**
	 * Load object from XML
	 *
	 * @access public
	 * @param SimpleXMLElement $xml
	 * @throws MoneybirdNotAcceptedException
	 */
	public function fromXML(SimpleXMLElement $xml) {
		throw new Exception(__CLASS__ . ' cannot be loaded from XML');
	}

	/**
	 * Convert object to XML
	 *
	 * @access public
	 * @return string
	 */
	public function toXML() {
		$xml = '<' . $this->type . '>' . PHP_EOL;
		$xml .= '  <ids>' . PHP_EOL;
		foreach ($this->ids as $id) {
			$xml .= '    <id>' . $id . '</id>' . PHP_EOL;
		}
		$xml .= '  </ids>' . PHP_EOL;
		$xml .= '</' . $this->type . '>' . PHP_EOL;
		return $xml;
	}

}