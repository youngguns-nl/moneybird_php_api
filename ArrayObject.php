<?php

/*
 * ArrayObject class file
 */

namespace Moneybird;

use \ArrayObject as ParentArrayObject;
use Moneybird\ArrayObject\TypeMismatchException;
use Moneybird\ArrayObject\UndefinedMethodException;

/**
 * Abstract class for array objects
 *
 * @abstract
 */
abstract class ArrayObject extends ParentArrayObject {

	/**
	 * Append an object to the array
	 * The object must match the type the array is meant for
	 *
	 * @param mixed $value
	 * @throws TypeMismatchException
	 * @return ArrayObject
	 */
	public function append($value) {
		$arrayType = $this->getChildName();
		if (!($value instanceof $arrayType)) {
			throw new TypeMismatchException('Passed argument is not an instance of ' . $arrayType);
		} else {
			parent::append($value);
		}
		return $this;
	}

	/**
	 * Merge another array of the same type into this array
	 *
	 * @param ArrayObject $array
	 * @throws TypeMismatchException
	 * @return ArrayObject
	 */
	public function merge(ArrayObject $array) {
		if (!($array instanceof $this)) {
			throw new TypeMismatchException('Passed argument is not an instance of ' . get_class($this));
		} else {
			foreach ($array as $object) {
				$this->append($object);
			}
		}
		return $this;
	}

	/**
	 * If method does not exists on array, call method on children
	 * Returns an array with return values
	 *
	 * @param string $method
	 * @param array $arguments
	 * @throws UndefinedMethodException
	 * @return Array
	 */
	public function __call($method, Array $arguments) {
		$arrayType = $this->getChildName();
		if (!method_exists($arrayType, $method)) {
			throw new UndefinedMethodException('Fatal error: Call to undefined method ' . $arrayType . '::' . $method . '()');
		}

		$return = array();
		foreach ($this as $object) {
			$return[] = call_user_func_array(array($object, $method), $arguments);
		}
		return $return;
	}

	/**
	 * Get classname of expected children
	 *
	 * @access $protected
	 * @return string
	 */
	protected function getChildName() {
		return substr(get_class($this), 0, -12); // 12 = strlen(\ArrayObject)
	}

	/**
	 * Create JSON representation of array
	 *
	 * @return string
	 */
	public function toJson() {
		$array = array();
		foreach ($this->disclose() as $key => $disclosure) {
			$values = $disclosure->toArray();
			foreach ($values as &$value) {
				$value = utf8_encode($value);
			}
			$array[$key] = $values;
		}
		return json_encode($array);
	}

	/**
	 * Create PHP array
	 * @return Array
	 */
	public function toArray(){
		$array = array();
		foreach($this as $value){
			$array[] = $value;
		}
		return $array;
	}
}