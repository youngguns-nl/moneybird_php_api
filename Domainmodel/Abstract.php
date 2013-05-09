<?php

/*
 * Domainmodel_Abstract class file
 */

namespace Moneybird;

/**
 * Base class for domain models
 *
 * @abstract
 */
abstract class Domainmodel_Abstract {

	/**
	 * Disclosure
	 *
	 * @var Disclosure
	 * @access protected
	 */
	protected $_disclosure;
	
	
	/**
	 * Array containing attributes to disclose
	 *
	 * @var array
	 * @access protected
	 */
	protected $_discloseAttr = array();
	
	
	/**
	 * Array of attributes that can't be modified
	 * @var Array
	 */
	protected $_readonlyAttr = array();
	
	/**
	 * Array of attributes that are required
	 * @var Array
	 */
	protected $_requiredAttr = array();

	
	/**
	 * Construct a new object and extract data
	 *
	 * @param array $data
	 */
	public function __construct(array $data = array()) {
		$this->init();
		$this->extract($data);
	}
	
	/**
	 * Initialize vars 
	 */
	protected function _initVars() {
	}

	/**
	 * Initialize 
	 */
	final protected function init(){
		/**
		 * Execute all _init methods
		 */
		foreach (get_class_methods($this) as $method){
			if (0 === strpos($method, '_init')){
				$this->$method();
			}
		}
	}
	
	/**
	 * Create disclosedAttributes array
	 */
	protected function _initDisclosedAttributes() {
		foreach (array_keys(get_class_vars(get_class($this))) as $property){
			if (substr($property, 0, 1) != '_') {
				$this->_discloseAttr[] = $property;
			}
		}
	}

	/**
	 * Sets data
	 * @param array $data
	 */
	public function setData(array $data = array()) {
		$this->extract(
			$data, 
			array_merge(
				array('id'),
				$this->_readonlyAttr
			)
		);
	}
	
	/**
	 * Discloses all values of the object that should be visible in the view layer.
	 *
	 * @param mixed $key
	 * @access public
	 * @return mixed
	 */
	public function disclose($key = null) {
		if (null === $this->_disclosure) {
			$this->_disclosure = new Disclosure($this->selfToArray(
				$this->_discloseAttr
			));
		}
		return (null === $key) ? $this->_disclosure : $this->_disclosure->__get($key);
	}
	
	/**
	 * Return the objects id or null
	 *
	 * @return int
	 */
	public function getId() {
		if (isset($this->id)) {
			return $this->id;
		} else {
			return null;
		}
	}

	/**
	 * Magic set method
	 * Do not allow set
	 *
	 * @param string $name
	 * @param mixed $value
	 * @throws Domainmodel_Exception
	 */
	public function __set($name, $value) {
		throw new Domainmodel_Exception('Don\'t set ('.$name.'), use setData');
	}
	
	/**
	 * Proxy to disclose method
	 * @param String $key
	 * @return mixed
	 */
	public function __get($key) {
		return $this->disclose($key);
	}
	
	/**
	 * Returns an array representation of this object
	 * @return array
	 * @access public
	 */
	public function toArray() {
		return $this->disclose()->toArray();
	}
	
	/**
	 * Returns an array representation of this object
	 * @return array
	 * @access protected
	 */
	protected function selfToArray(Array $filter = array()) {
		$filter = array_flip($filter);
		$hasFilter = count($filter) > 0;
		$values = array();
		foreach ($this as $key => $value) {
			if (!$hasFilter || isset($filter[$key])) {
				$values[$key] = $this->$key;
			}
		}
		return $values;
	}
	
	/**
	 * Extract will take an array and try to automatically map the array values
	 * to properties in this object
	 *
	 * @param Array $values
	 * @param Array $filter
	 * @access protected
	 */
	protected function extract(Array $values, $filter=array()) {
		$this->_disclosure = null;
		foreach (array_keys(get_object_vars($this)) as $key) {
			if (isset($filter[$key]) || !array_key_exists($key, $values)) {
				continue;
			}
			/**
			 * setting attributes starting with an underscore is not allowed
			 */
			if (substr($key, 0, 1) !== '_') {
				$method = 'set' . ucfirst($key) . 'Attr';
				/**
				 * Check if a set method exists for this key, else
				 * assign the value to the attribute if a value exists
				 */
				if (method_exists($this, $method)) {
					$this->$method((isset($values[$key]) ? $values[$key] : null));
				} elseif (isset($values[$key])) {
					$this->$key = $values[$key];
				}
			}
		}
	}
		
	/**
	 * Adopt the data from $self
	 * @param Domainmodel_Abstract $self
	 * @return self
	 */
	protected function reload(Domainmodel_Abstract $self) {
		$this->_initVars();
		$this->extract($self->selfToArray());
		return $this;
	}

	/**
	 * Copy the object
	 * @param Array $filter Attributes not to copy
	 * @return self
	 */
	protected function copy(Array $filter = array()) {
		$filter = array_flip(array_merge($filter, $this->_readonlyAttr));

		$copy = new $this();
		$attributes = $this->selfToArray();
		foreach ($attributes as $key => &$value) {
			if (array_key_exists($key, $filter)) {
				unset($attributes[$key]);
			}
			elseif ($value instanceof ArrayObject) {
				try {
					$newElements = $value->copy($filter);
					$value = new $value;
					foreach ($newElements as $elmCopy) {
						$value->append($elmCopy);
					}
				} catch (ArrayObject_UndefinedMethodException $e) {
					// pass
				}
			}
			elseif ($value instanceof Domainmodel_Abstract) {
				$value = $value->copy($filter);
			}
		}
		$copy->setData($attributes);
		return $copy;
	}

	/**
	 * Validate object
	 * @return bool
	 */
	protected function validate() {
		foreach ($this->_requiredAttr as $attr) {
			if (is_array($attr)) {
				$valid = false;
				foreach ($attr as $sub) {
					if (!is_null($this->$sub)) {
						$valid = true;
						break;
					}
				}
				if (!$valid) {
					return false;
				}
			} elseif (is_null($this->$attr)) {
				return false;
			}
		}
		return true;
	}
	
}