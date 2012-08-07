<?php

/*
 * Product class file
 */

namespace Moneybird;

/**
 * Product
 */
class Product extends Domainmodel_Abstract implements Mapper_Mapable {
	
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