<?php

/*
 * Abstract for envelopes (send info)
 */

namespace Moneybird;

/**
 * Envelope
 * @abstract
 */
abstract class Envelope_Abstract extends Domainmodel_Abstract implements Mapper_Mapable {
	
	protected $email;
	protected $sendMethod;
	
	/**
	 * Array of properties that are only disclosed if they are not empty
	 * @var Array
	 */
	protected $_discloseNotEmpty = array('email');
	
	/**
	 * Discloses all values of the object that should be visible in the view layer.
	 *
	 * @param mixed $key
	 * @access public
	 * @return mixed
	 */
	public function disclose($key = null) {
		$this->_discloseAttr = array('sendMethod');
		foreach ($this->_discloseNotEmpty as $attr) {
			if (!empty($this->$attr)) {
				$this->_discloseAttr[] = $attr;
			}
		}
		return parent::disclose($key);
	}
	
	/**
	 * Set send method
	 * @param string $value
	 * @throws Envelope_InvalidMethodException
	 */
	protected function setSendMethodAttr($value) {
		if (!in_array($value, array('hand', 'email', 'post'))) {
			throw new Envelope_InvalidMethodException('Invalid send method: ' . $value);
		}

		$this->sendMethod = $value;
	}	

}
