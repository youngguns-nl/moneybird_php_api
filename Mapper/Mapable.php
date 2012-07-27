<?php

/*
 * Mapper Mapable
 */

namespace Moneybird;

/**
 * Mapper Mapables can be mapped
 */
interface Mapper_Mapable {
	
	/**
	 * Get array representation of Subject
	 * @return Array
	 */
	public function toArray();
}