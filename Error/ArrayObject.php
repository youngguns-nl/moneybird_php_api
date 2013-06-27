<?php

/*
 * Error array file
 */

namespace Moneybird\Error;

use Moneybird\ArrayObject as ParentArrayObject;
use Moneybird\Mapper\Mapable;

/**
 * Error array
 */
class ArrayObject extends ParentArrayObject implements Mapable {
	
	/**
	 * String representation of object
	 * @return string
	 */
	public function __toString() {
		$strings = array();
		foreach ($this as $object) {
			$strings[] = $object->__toString();
		}
		return implode(PHP_EOL, $strings);
	}
	
}