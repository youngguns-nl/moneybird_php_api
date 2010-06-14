<?php

/**
 * Interface for MoneybirdObject
 *
 */
interface iMoneybirdObject
{
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
abstract class MoneybirdObject implements iMoneybirdObject
{
	/**
	 * Properties
	 *
	 * @access protected
	 * @var array
	 */
	protected $properties;

	/**
	 * Set property
	 *
	 * @access public
	 * @param string $property
	 * @param mixed $value
	 * @throws Exception
	 */
	public function __set($property, $value)
	{
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
	public function __get($property)
	{
		if (!isset($this->properties[$property]))
		{
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
	public function fromXML(SimpleXMLElement $xml, Array $arrayHandlers = null)
	{
		foreach ($xml as $key => $value)
		{
			$attributes = $value->attributes();
			$type = isset($attributes['type']) ? $attributes['type'] : 'string';

			$key = str_replace('-', '_', $key);

			if (isset($attributes['nil']) && $attributes['nil'] == 'true')
			{
				$this->properties[$key] = null;
			}
			elseif ($type == 'array')
			{
				$this->properties[$key] = array();
				if (isset($arrayHandlers[$key]))
				{
					foreach ($value as $subxml)
					{
						$object = new $arrayHandlers[$key]($this);
						$object->fromXML($subxml);
						$this->properties[$key][] = $object;
					}
				}
			}
			elseif ($type == 'integer')
			{
				$this->properties[$key] = intval($value);
			}
			elseif ($type == 'float')
			{
				$this->properties[$key] = floatval($value);
			}
			elseif ($type == 'boolean')
			{
				$this->properties[$key] = $value == 'true';
			}
			elseif ($type == 'datetime' || $type == 'date')
			{
				$this->properties[$key] = new DateTime(strval($value));
			}
			else
			{
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
	public function toXML(Array $arrayHandlers = null, $elmKeyOpen = null, $elmKeyClose = null, array $skipProperties = array())
	{
		if (is_a($this, 'iMoneybirdContact'))
		{
			$root = 'contact';
		}
		elseif (is_a($this, 'iMoneybirdInvoice'))
		{
			$root = 'invoice';
		}
		elseif (is_a($this, 'iMoneybirdRecurringTemplate'))
		{
			$root = 'recurringtemplate';
		}
		else
		{
			// Guess
			$root = strtolower(substr(get_class($this), 9));
			//throw new MoneybirdUnknownTypeException('Unknown type: '.get_class($this));
		}

		if ($elmKeyOpen == null)
		{
			$elmKeyOpen = '<'.$root.'>';
		}
		if ($elmKeyClose == null)
		{
			$elmKeyClose = '</'.$root.'>';
		}

		$skipProperties = array_merge($skipProperties, array(
			'updated_at', 'created_at',
		));

		$xml = $elmKeyOpen.PHP_EOL;
		foreach ($this->properties as $key => $value)
		{
			if (in_array($key, $skipProperties))
			{
				continue;
			}

			$key = str_replace('_', '-', $key);

			$keyOpen  = '<'.$key.'>';
			$keyClose = '</'.$key.'>';

			if (is_object($value) && $value instanceof DateTime)
			{
				$value = $value->format('c');
			}
			elseif (is_array($value))
			{
				if (isset($arrayHandlers[$key]))
				{
					$keyOpen  = '<'.$arrayHandlers[$key].' type="array">'.PHP_EOL;
					$keyClose = '</'.$arrayHandlers[$key].'>';
					$newvalue = '';
					foreach ($value as $object)
					{
						$newvalue .= $object->toXML();
					}
					$value = $newvalue;
				}
				else
				{
					foreach ($value as $arrayValue)
					{
						$xml .= '   '.$keyOpen.htmlspecialchars($arrayValue).$keyClose.PHP_EOL;
					}
					continue;
				}
			}
			elseif (is_null($value))
			{
				$keyOpen = substr($keyOpen, 0, -1).' nil="true">';
			}
			else
			{
				$value = htmlspecialchars($value);
			}
			$xml .= '   '.$keyOpen.$value.$keyClose.PHP_EOL;
		}
		$xml .= $elmKeyClose.PHP_EOL;

		return $xml;
	}
}