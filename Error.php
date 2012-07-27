<?php

/*
 * Error class file
 */

namespace Moneybird;

/**
 * Error
 */
class Error extends Domainmodel_Abstract implements Mapper_Mapable {
	
	protected $attribute;
	protected $message;
	
	/**
	 * String representation of object
	 * @return string
	 */
	public function __toString() {
		return $this->attribute.': '.$this->message;
	}
}
