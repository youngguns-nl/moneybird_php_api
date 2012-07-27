<?php

/*
 * Mapper interface file
 */

namespace Moneybird;

/**
 * Mapper interface
 */
interface Mapper {

	/**
	 * Create object from string
	 * @param string $string 
	 * @return Mapper_Mapable
	 * @access public
	 */
	public function mapFromStorage($string);
	
	/**
	 * Map object
	 * @access public
	 * @param Mapper_Mapable $subject Object to map
	 * @return string
	 */
	public function mapToStorage(Mapper_Mapable $subject);
}