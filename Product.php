<?php

/*
 * Product class file
 */

namespace Moneybird;

use Moneybird\Domainmodel\AbstractModel;
use Moneybird\Mapper\Mapable;

/**
 * Product
 */
class Product extends AbstractModel implements Mapable {
	
	protected $createdAt;
	protected $description;
	protected $id;
	protected $ledgerAccountId;
	protected $price;
	protected $tax;
	protected $taxRateId;
	protected $updatedAt;
	
	protected $_readonlyAttr = array(
		'createdAt',
		'description',
		'id',
		'ledgerAccountId',
		'price',
		'tax',
		'taxRateId',
		'updatedAt',
	);
}