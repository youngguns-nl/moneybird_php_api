<?php

/*
 * Mapper interface file
 */

namespace Moneybird;

use Moneybird\Mapper\Mapable;

/**
 * Mapper interface
 */
interface Mapper {

	/**
	 * Create object from string
	 * @param string $string 
	 * @return Mapable
	 * @access public
	 */
	public function mapFromStorage($string);
	
	/**
	 * Map object
	 * @access public
	 * @param Mapable $subject Object to map
	 * @return string
	 */
	public function mapToStorage(Mapable $subject);
	
	/**
	 * Returns the content type of mapped objects
	 * 
	 * @return string
	 */
	public function getContentType();
}