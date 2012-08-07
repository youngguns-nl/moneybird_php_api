<?php

/*
 * Detail_Abstract class file
 */

namespace Moneybird;

/**
 * Detail_Abstract
 * @abstract
 */
abstract class Detail_Abstract 
	extends 
		Domainmodel_Abstract 
	implements 
		Mapper_Mapable, 
		DeleteBySaving {
	
	protected $amount; 
	protected $createdAt;
	protected $description;
	protected $id; 
	protected $ledgerAccountId;
	protected $price;
	protected $rowOrder;
	protected $tax;
	protected $taxRateId;
	protected $totalPriceExclTax;
	protected $totalPriceInclTax;
	protected $updatedAt;
	
	protected $_deleted = false;
	
	protected $_readonlyAttr = array(
		'createdAt',
		'id', 
		'invoiceId',
		'totalPriceExclTax',
		'totalPriceInclTax',
		'updatedAt', 
	);
	
	/**
	 * Mark deleted
	 */
	public function setDeleted() {
		$this->_deleted = true;
	}
	
	/**
	 * Get delete status
	 * @return bool
	 */
	public function isDeleted() {
		return $this->_deleted;
	}
}
