<?php

/*
 * Error array file
 */

namespace Moneybird;

/**
 * Error array
 */
class Error_Array extends ArrayObject implements Mapper_Mapable {
	
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