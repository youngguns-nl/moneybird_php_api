<?php

/*
 * Mapable
 */

namespace Moneybird\Mapper;

/**
 * Mapables can be mapped
 */
interface Mapable {
	
	/**
	 * Get array representation of Subject
	 * @return Array
	 */
	public function toArray();
}