<?php

/*
 * AbstractDetail class file
 */

namespace Moneybird\Detail;

use Moneybird\Domainmodel\AbstractModel;
use Moneybird\Mapper\Mapable;
use Moneybird\DeleteBySaving;

/**
 * DetailAbstract
 * @abstract
 */
abstract class AbstractDetail
	extends 
		AbstractModel
	implements 
		Mapable, 
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
