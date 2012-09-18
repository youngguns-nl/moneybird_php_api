<?php

/*
 * Invoice sync class
 */

namespace Moneybird;

/**
 * Invoice sync
 */
class Invoice_Sync extends Domainmodel_Abstract implements Mapper_Mapable, SyncObject {
	
	protected $id = array();
	
	/**
	 * Set Id
	 * @param array $value
	 * @throws InvalidIdException
	 */
	protected function setIdAttr(Array $value) {
		if (!is_null($value)) {
			foreach ($value as $id) {
				if (!preg_match('/^[0-9]+$/D', $id)) {
					throw new InvalidIdException('Invalid id: ' . $id);
				}
			}
		}

		$this->id = $value;
	}
}